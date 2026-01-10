<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with(['teacher', 'subject.grade']);

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by subject
        if ($request->has('subject_id') && $request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }

        $courses = $query->latest()->paginate(15);
        $subjects = Subject::with('grade')->get();

        return view('admin.courses.index', compact('courses', 'subjects'));
    }

    public function create()
    {
        $teachers = \App\Models\User::role('teacher')->get();
        return view('admin.courses.create', compact('teachers'));
    }

    /**
     * Get subjects by grade (AJAX endpoint) - REMOVED, using text inputs now
     */
    public function getSubjectsByGrade(Request $request)
    {
        // This endpoint is no longer needed, but keeping for backward compatibility
        return response()->json(['subjects' => []]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'grade_name' => 'required|string|max:255',
            'subject_name' => 'required|string|max:255',
            'main_teacher_id' => 'required|exists:users,id',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'exists:users,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:books,slug',
            'short_description' => 'nullable|string|max:200',
            'description' => 'nullable|string',
            'language' => 'nullable|in:en,ur,ar,other',
            'difficulty_level' => 'nullable|in:beginner,intermediate,advanced,all',
            'course_level' => 'nullable|in:elementary,secondary,higher_secondary,undergraduate,graduate,professional',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'access_duration_months' => 'nullable|integer|min:1',
            'max_enrollments' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'thumbnail' => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:2048',
            'intro_video_url' => 'nullable|string',
            'intro_video_file' => 'nullable|file|mimes:mp4,avi,mov,wmv,flv|max:102400',
            'intro_video_provider' => 'nullable|in:youtube,vimeo,upload,bunny',
            'status' => 'required|in:draft,pending,published,approved,rejected',
            'certificate_enabled' => 'boolean',
            'reviews_enabled' => 'boolean',
            'comments_enabled' => 'boolean',
            'is_featured' => 'boolean',
            'is_popular' => 'boolean',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
        ]);

        // Generate slug if not provided
        $slug = $request->slug ?? \Illuminate\Support\Str::slug($request->title);
        $slugCount = Book::where('slug', $slug)->count();
        if ($slugCount > 0) {
            $slug = $slug . '-' . ($slugCount + 1);
        }

        // Try to find or create Grade and Subject by name
        $subject = null;
        $grade = null;
        
        if ($request->grade_name) {
            $grade = \App\Models\Grade::firstOrCreate(
                ['name' => $request->grade_name],
                ['slug' => \Illuminate\Support\Str::slug($request->grade_name), 'is_active' => true]
            );
        }
        
        if ($request->subject_name && $grade) {
            $subject = \App\Models\Subject::firstOrCreate(
                [
                    'name' => $request->subject_name,
                    'grade_id' => $grade->id
                ],
                [
                    'slug' => \Illuminate\Support\Str::slug($request->subject_name),
                    'is_active' => true
                ]
            );
        }

        $data = [
            'subject_id' => $subject ? $subject->id : null,
            'grade_name' => $request->grade_name,
            'subject_name' => $request->subject_name,
            'teacher_id' => $request->main_teacher_id, // Main teacher/creator
            'title' => $request->title,
            'slug' => $slug,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'language' => $request->language ?? 'en',
            'difficulty_level' => $request->difficulty_level ?? 'all',
            'course_level' => $request->course_level,
            'what_you_will_learn' => $request->what_you_will_learn,
            'course_requirements' => $request->course_requirements,
            'target_audience' => $request->target_audience,
            'price' => $request->has('is_free') && $request->is_free ? 0 : ($request->price ?? 0),
            'is_free' => $request->has('is_free') ? true : false,
            'access_duration_months' => $request->access_duration_months ?? 12,
            'max_enrollments' => $request->max_enrollments,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'certificate_enabled' => $request->has('certificate_enabled') ? true : false,
            'reviews_enabled' => $request->has('reviews_enabled') ? true : true,
            'comments_enabled' => $request->has('comments_enabled') ? true : true,
            'is_featured' => $request->has('is_featured') ? true : false,
            'is_popular' => $request->has('is_popular') ? true : false,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ];

        // Process tags
        if ($request->has('tags') && is_string($request->tags)) {
            $tags = json_decode($request->tags, true);
            if (is_array($tags)) {
                $data['tags'] = $tags;
            }
        }

        // Process learning objectives (from what_you_will_learn field)
        if ($request->has('what_you_will_learn') && !empty($request->what_you_will_learn)) {
            // Convert text to array (split by newlines or bullets)
            $objectives = preg_split('/\n|•|\*|-/', $request->what_you_will_learn);
            $objectives = array_map('trim', $objectives);
            $objectives = array_filter($objectives); // Remove empty items
            if (!empty($objectives)) {
                $data['learning_objectives'] = array_values($objectives);
            }
        }

        // Process meta keywords
        if ($request->has('meta_keywords') && is_string($request->meta_keywords)) {
            $keywords = json_decode($request->meta_keywords, true);
            if (is_array($keywords)) {
                $data['meta_keywords'] = $keywords;
            }
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('courses/covers', 'public');
        }

        // Handle intro video upload
        if ($request->has('intro_video_provider') && $request->intro_video_provider) {
            $data['intro_video_provider'] = $request->intro_video_provider;
            
            if ($request->intro_video_provider === 'upload' && $request->hasFile('intro_video_file')) {
                $data['intro_video_url'] = $request->file('intro_video_file')->store('courses/intro-videos', 'public');
            } elseif ($request->has('intro_video_url') && $request->intro_video_url) {
                $data['intro_video_url'] = $request->intro_video_url;
            }
        }

        $course = Book::create($data);

        // Assign multiple teachers (co-teachers)
        if ($request->has('teacher_ids') && is_array($request->teacher_ids)) {
            $teacherIds = array_unique($request->teacher_ids);
            $coTeacherCount = 0;
            
            foreach ($teacherIds as $teacherId) {
                // Skip main teacher (already assigned as teacher_id)
                if ($teacherId == $course->teacher_id) {
                    continue;
                }
                
                // Attach as co-teacher (avoid duplicates)
                if (!$course->teachers()->where('users.id', $teacherId)->exists()) {
                    $course->teachers()->attach($teacherId, [
                        'role' => 'co-teacher'
                    ]);
                    $coTeacherCount++;
                }
            }
            
            $totalTeachers = 1 + $coTeacherCount; // 1 main teacher + co-teachers
            return redirect()->route('admin.courses.index')
                ->with('success', "Course created successfully with {$totalTeachers} teacher(s).");
        }

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function show($id)
    {
        $course = Book::with(['teacher', 'subject.grade', 'chapters.lessons.topics'])
            ->findOrFail($id);

        return view('admin.courses.show', compact('course'));
    }

    public function edit($id)
    {
        $course = Book::findOrFail($id);
        $grades = Grade::with('subjects')->get();
        $teachers = \App\Models\User::role('teacher')->get();

        return view('admin.courses.edit', compact('course', 'grades', 'teachers'));
    }

    public function update(Request $request, $id)
    {
        $course = Book::findOrFail($id);

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'duration_months' => 'nullable|integer|min:1',
            'cover_image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $data = [
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price ?? 0,
            'is_free' => $request->has('is_free') ? true : false,
            'duration_months' => $request->duration_months ?? $course->duration_months,
            'status' => $request->status,
        ];

        if ($request->hasFile('cover_image')) {
            if ($course->cover_image) {
                Storage::disk('public')->delete($course->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('courses', 'public');
        }

        $course->update($data);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy($id)
    {
        $course = Book::findOrFail($id);

        if ($course->cover_image) {
            Storage::disk('public')->delete($course->cover_image);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    public function approve($id)
    {
        $course = Book::findOrFail($id);
        $course->update(['status' => 'published']);

        return redirect()->back()
            ->with('success', 'Course approved and published.');
    }

    public function reject($id)
    {
        $course = Book::findOrFail($id);
        $course->update(['status' => 'draft']);

        return redirect()->back()
            ->with('success', 'Course rejected and set to draft.');
    }
}
