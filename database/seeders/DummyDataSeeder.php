<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\CourseEnrollment;
use App\Models\CourseTeacher;
use App\Models\Grade;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds comprehensive dummy data: grades, subjects, users, courses, chapters, lessons,
     * topics, payments, enrollments, lesson progress, and course teachers.
     */
    public function run(): void
    {
        // 1. Grades
        $grades = [];
        $gradeNames = ['Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        foreach ($gradeNames as $i => $name) {
            $grades[] = Grade::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => "Curriculum for {$name}",
                    'order' => $i + 1,
                    'is_active' => true,
                ]
            );
        }

        // 2. Subjects (3–4 per grade)
        $subjects = [];
        $subjectTemplates = [
            ['name' => 'Mathematics', 'icon' => 'calculator'],
            ['name' => 'Physics', 'icon' => 'atom'],
            ['name' => 'Chemistry', 'icon' => 'flask'],
            ['name' => 'English', 'icon' => 'book'],
            ['name' => 'Urdu', 'icon' => 'language'],
            ['name' => 'Computer Science', 'icon' => 'laptop'],
        ];
        foreach ($grades as $grade) {
            foreach ($subjectTemplates as $s) {
                $slug = Str::slug($grade->name . '-' . $s['name']);
                $subjects[] = Subject::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'grade_id' => $grade->id,
                        'name' => $s['name'],
                        'description' => "{$s['name']} for {$grade->name}",
                        'icon' => $s['icon'],
                        'order' => count($subjects),
                        'is_active' => true,
                    ]
                );
            }
        }

        // 3. Extra users (students and teachers). RoleSeeder already creates admin, teacher, student.
        $teacherRole = Role::where('name', 'teacher')->first();
        $studentRole = Role::where('name', 'student')->first();

        $extraTeachers = [
            ['name' => 'Sarah Ahmad', 'email' => 'sarah.ahmad@example.com', 'mobile' => '03211234501'],
            ['name' => 'Ali Hassan', 'email' => 'ali.hassan@example.com', 'mobile' => '03211234502'],
        ];
        $extraStudents = [
            ['name' => 'Fatima Khan', 'email' => 'fatima.khan@example.com', 'mobile' => '03331234501'],
            ['name' => 'Omar Siddiqui', 'email' => 'omar.siddiqui@example.com', 'mobile' => '03331234502'],
            ['name' => 'Ayesha Malik', 'email' => 'ayesha.malik@example.com', 'mobile' => '03331234503'],
            ['name' => 'Usman Sheikh', 'email' => 'usman.sheikh@example.com', 'mobile' => '03331234504'],
        ];

        $teachers = [User::where('email', 'teacher@kitabasan.com')->first()];
        foreach ($extraTeachers as $t) {
            $u = User::firstOrCreate(
                ['email' => $t['email']],
                [
                    'name' => $t['name'],
                    'mobile' => $t['mobile'],
                    'password' => Hash::make('password'),
                    'status' => 'active',
                ]
            );
            if (!$u->hasRole('teacher')) {
                $u->assignRole($teacherRole);
            }
            $teachers[] = $u;
        }

        $students = [User::where('email', 'student@kitabasan.com')->first()];
        foreach ($extraStudents as $s) {
            $u = User::firstOrCreate(
                ['email' => $s['email']],
                [
                    'name' => $s['name'],
                    'mobile' => $s['mobile'],
                    'password' => Hash::make('password'),
                    'status' => 'active',
                ]
            );
            if (!$u->hasRole('student')) {
                $u->assignRole($studentRole);
            }
            $students[] = $u;
        }

        // 4. Teacher profiles for all teachers
        foreach ($teachers as $t) {
            TeacherProfile::firstOrCreate(
                ['user_id' => $t->id],
                [
                    'bio' => "Experienced educator with focus on student success.",
                    'qualifications' => 'MPhil, BEd',
                    'specializations' => 'Science and Mathematics',
                    'total_courses' => 0,
                    'total_students' => 0,
                    'rating' => 4.5,
                    'rating_count' => 12,
                    'status' => 'active',
                ]
            );
        }

        // 5. Books (courses) – mix of free/paid, published/draft
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('order')->get();
        $pmId = $paymentMethods->isNotEmpty() ? $paymentMethods->first()->id : null;

        $courseData = [
            [
                'title' => 'Introduction to Algebra',
                'subject' => 'Mathematics',
                'grade' => 'Grade 9',
                'price' => 0,
                'is_free' => true,
                'status' => 'published',
                'teacher_idx' => 0,
                'learning_objectives' => ['Solve linear equations', 'Understand variables', 'Graph linear functions'],
            ],
            [
                'title' => 'Physics: Mechanics and Motion',
                'subject' => 'Physics',
                'grade' => 'Grade 10',
                'price' => 2500,
                'is_free' => false,
                'status' => 'published',
                'teacher_idx' => 0,
                'learning_objectives' => ['Newton\'s laws', 'Kinematics', 'Forces and energy'],
            ],
            [
                'title' => 'Organic Chemistry Basics',
                'subject' => 'Chemistry',
                'grade' => 'Grade 11',
                'price' => 3000,
                'is_free' => false,
                'status' => 'published',
                'teacher_idx' => 1,
                'learning_objectives' => ['Hydrocarbons', 'Functional groups', 'Reaction mechanisms'],
            ],
            [
                'title' => 'English Grammar and Composition',
                'subject' => 'English',
                'grade' => 'Grade 9',
                'price' => 1500,
                'is_free' => false,
                'status' => 'published',
                'teacher_idx' => 1,
                'learning_objectives' => ['Parts of speech', 'Sentence structure', 'Essay writing'],
            ],
            [
                'title' => 'Programming with Python',
                'subject' => 'Computer Science',
                'grade' => 'Grade 10',
                'price' => 0,
                'is_free' => true,
                'status' => 'published',
                'teacher_idx' => 2,
                'learning_objectives' => ['Variables and types', 'Loops and functions', 'Basic projects'],
            ],
            [
                'title' => 'Urdu Literature – Prose and Poetry',
                'subject' => 'Urdu',
                'grade' => 'Grade 12',
                'price' => 2000,
                'is_free' => false,
                'status' => 'published',
                'teacher_idx' => 2,
                'learning_objectives' => ['Classic prose', 'Poetry forms', 'Critical analysis'],
            ],
            [
                'title' => 'Advanced Mathematics (Draft)',
                'subject' => 'Mathematics',
                'grade' => 'Grade 12',
                'price' => 4000,
                'is_free' => false,
                'status' => 'draft',
                'teacher_idx' => 0,
                'learning_objectives' => ['Calculus basics', 'Vectors', 'Statistics'],
            ],
        ];

        $books = [];
        foreach ($courseData as $c) {
            $subject = Subject::where('name', $c['subject'])
                ->whereHas('grade', fn ($q) => $q->where('name', $c['grade']))
                ->first()
                ?? Subject::where('name', $c['subject'])->first();
            if (!$subject) {
                $grade = Grade::where('name', $c['grade'])->first() ?? Grade::first();
                $slug = Str::slug($grade->name . '-' . $c['subject']);
                $subject = Subject::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'grade_id' => $grade->id,
                        'name' => $c['subject'],
                        'description' => "{$c['subject']} for {$c['grade']}",
                        'order' => 0,
                        'is_active' => true,
                    ]
                );
            }
            $teacher = $teachers[$c['teacher_idx']] ?? $teachers[0];
            $baseSlug = Str::slug($c['title']);
            $slug = $baseSlug;
            $n = 0;
            while (Book::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . (++$n);
            }
            $books[] = Book::firstOrCreate(
                ['slug' => $slug],
                [
                    'subject_id' => $subject->id,
                    'grade_name' => $c['grade'],
                    'subject_name' => $c['subject'],
                    'teacher_id' => $teacher->id,
                    'title' => $c['title'],
                    'description' => "Comprehensive course on {$c['title']}. " . implode(' ', $c['learning_objectives']),
                    'short_description' => "Learn {$c['title']} with step-by-step lessons.",
                    'price' => $c['price'],
                    'is_free' => $c['is_free'],
                    'access_duration_months' => 12,
                    'status' => $c['status'],
                    'order' => count($books),
                    'total_lessons' => 0,
                    'total_duration' => 0,
                    'enrollment_count' => 0,
                    'rating' => 4.2,
                    'rating_count' => 0,
                    'learning_objectives' => $c['learning_objectives'],
                    'what_you_will_learn' => implode("\n", array_map(fn ($o) => "• $o", $c['learning_objectives'])),
                    'language' => 'en',
                    'difficulty_level' => 'intermediate',
                    'is_featured' => count($books) < 2,
                    'is_popular' => count($books) < 4,
                ]
            );
        }

        // 6. Modules (optional, for first 2 books)
        $modules = [];
        foreach (array_slice($books, 0, 2) as $book) {
            $modules[] = Module::firstOrCreate(
                ['book_id' => $book->id, 'title' => 'Module 1: Foundations'],
                ['description' => 'Core concepts and basics.', 'order_index' => 0, 'is_active' => true]
            );
            $modules[] = Module::firstOrCreate(
                ['book_id' => $book->id, 'title' => 'Module 2: Advanced Topics'],
                ['description' => 'In-depth material.', 'order_index' => 1, 'is_active' => true]
            );
        }

        // 7. Chapters (2–4 per book; link to module when exists)
        $chapters = [];
        $moduleByBook = collect($modules)->groupBy('book_id');
        foreach ($books as $book) {
            $bookModules = $moduleByBook->get($book->id, collect());
            $titles = ['Getting Started', 'Core Concepts', 'Practice and Applications', 'Summary and Review'];
            foreach (array_slice($titles, 0, rand(2, 4)) as $i => $title) {
                $mod = $bookModules->get($i);
                $chapters[] = Chapter::firstOrCreate(
                    ['book_id' => $book->id, 'title' => $title, 'order' => $i],
                    [
                        'module_id' => $mod?->id,
                        'description' => "This chapter covers: $title.",
                        'chapter_type' => 'video',
                        'order' => $i,
                        'is_free' => $i === 0,
                        'is_preview' => $i === 0,
                        'is_active' => true,
                    ]
                );
            }
        }

        // 8. Lessons (2–3 per chapter)
        $youtubeIds = ['dQw4w9WgXcQ', 'jNQXAC9IVRw', '9bZkp7q19f0', 'RgKAFK5djSk', 'kJQP7kiw5Fk'];
        $lessons = [];
        foreach ($chapters as $ch) {
            $lessonTitles = ['Introduction to the Topic', 'Detailed Explanation', 'Examples and Practice'];
            foreach (array_slice($lessonTitles, 0, rand(2, 3)) as $j => $lt) {
                $vid = $youtubeIds[array_rand($youtubeIds)];
                $lessons[] = Lesson::firstOrCreate(
                    ['chapter_id' => $ch->id, 'title' => $lt, 'order' => $j],
                    [
                        'description' => "Lesson: $lt",
                        'video_id' => $vid,
                        'video_host' => 'youtube',
                        'duration' => rand(300, 900),
                        'order' => $j,
                        'status' => 'published',
                        'is_free' => $j === 0,
                        'is_preview' => $j === 0,
                    ]
                );
            }
        }

        // 9. Topics (1–2 per lesson)
        foreach ($lessons as $les) {
            $topicTitles = ['Main Lecture', 'Key Points'];
            foreach (array_slice($topicTitles, 0, rand(1, 2)) as $k => $tt) {
                Topic::firstOrCreate(
                    ['lesson_id' => $les->id, 'title' => $tt, 'order' => $k],
                    [
                        'description' => "Topic: $tt",
                        'video_id' => $youtubeIds[array_rand($youtubeIds)],
                        'video_host' => 'youtube',
                        'duration' => rand(120, 600),
                        'type' => 'lecture',
                        'order' => $k,
                        'is_free' => $k === 0,
                    ]
                );
            }
        }

        // 10. Payments (completed, pending, failed) for paid books and some students
        $paidBooks = collect($books)->filter(fn ($b) => !$b->is_free)->values();
        $statuses = ['completed', 'completed', 'pending', 'failed'];
        $payments = [];
        foreach ($paidBooks->take(4) as $index => $book) {
            $stu = $students[$index % count($students)];
            $amount = (float) $book->price;
            $txn = 'TXN' . strtoupper(Str::random(16)) . (string) time();
            $status = $statuses[$index % 4];
            $payments[] = Payment::firstOrCreate(
                ['transaction_id' => $txn],
                [
                    'user_id' => $stu->id,
                    'book_id' => $book->id,
                    'gateway' => 'jazzcash',
                    'payment_method_id' => $pmId,
                    'amount' => $amount,
                    'currency' => 'PKR',
                    'status' => $status,
                    'paid_at' => $status === 'completed' ? now()->subDays(rand(1, 30)) : null,
                    'gateway_response' => $status === 'completed' ? ['result' => 'success'] : null,
                ]
            );
        }

        // 11. Course enrollments (paid + free)
        foreach ($payments as $p) {
            if ($p->status === 'completed') {
                CourseEnrollment::firstOrCreate(
                    ['user_id' => $p->user_id, 'book_id' => $p->book_id],
                    [
                        'payment_id' => $p->id,
                        'status' => 'active',
                        'payment_status' => 'paid',
                        'enrolled_at' => $p->paid_at ?? now(),
                        'expires_at' => now()->addMonths(12),
                        'progress_percentage' => rand(0, 80),
                        'last_accessed_at' => now()->subDays(rand(0, 5)),
                    ]
                );
            }
        }
        $freeBooks = collect($books)->filter(fn ($b) => $b->is_free)->values();
        foreach ($freeBooks as $book) {
            foreach (array_slice($students, 0, 3) as $stu) {
                CourseEnrollment::firstOrCreate(
                    ['user_id' => $stu->id, 'book_id' => $book->id],
                    [
                        'payment_id' => null,
                        'status' => 'active',
                        'payment_status' => 'free',
                        'enrolled_at' => now()->subDays(rand(5, 60)),
                        'expires_at' => null,
                        'progress_percentage' => rand(0, 100),
                        'last_accessed_at' => now()->subDays(rand(0, 3)),
                    ]
                );
            }
        }

        // 12. Lesson progress for some enrolled students
        $enrollments = CourseEnrollment::whereIn('payment_status', ['paid', 'free'])->get();
        foreach ($enrollments->take(8) as $en) {
            $bookChapters = Chapter::where('book_id', $en->book_id)->with('lessons')->get();
            $lessonsToProgress = $bookChapters->pluck('lessons')->flatten()->take(rand(1, 4));
            foreach ($lessonsToProgress as $les) {
                LessonProgress::firstOrCreate(
                    ['user_id' => $en->user_id, 'lesson_id' => $les->id],
                    [
                        'watch_percentage' => rand(20, 100),
                        'last_watched_position' => rand(0, 300),
                        'last_watched_at' => now()->subDays(rand(0, 2)),
                        'is_completed' => (bool) rand(0, 1),
                        'completed_at' => rand(0, 1) ? now()->subDay() : null,
                    ]
                );
            }
        }

        // 13. Co-teachers (add 2nd teacher to 2 courses)
        foreach (array_slice($books, 1, 2) as $book) {
            $coTeacher = $teachers[1] ?? $teachers[0];
            if ($book->teacher_id != $coTeacher->id) {
                CourseTeacher::firstOrCreate(
                    ['book_id' => $book->id, 'teacher_id' => $coTeacher->id],
                    ['role' => 'co-teacher']
                );
            }
        }

        // 14. Update book counts (total_lessons, enrollment_count)
        foreach ($books as $book) {
            $totalLessons = Lesson::whereHas('chapter', fn ($q) => $q->where('book_id', $book->id))->count();
            $enrollmentCount = CourseEnrollment::where('book_id', $book->id)->count();
            $book->update([
                'total_lessons' => $totalLessons,
                'enrollment_count' => $enrollmentCount,
                'total_duration' => $totalLessons * 10,
                'duration_hours' => (int) ceil($totalLessons * 10 / 60),
                'lectures_count' => $totalLessons,
            ]);
        }

        // 15. Teacher profile totals
        foreach ($teachers as $t) {
            $profile = TeacherProfile::where('user_id', $t->id)->first();
            if ($profile) {
                $bookIds = Book::where('teacher_id', $t->id)->pluck('id')
                    ->merge(CourseTeacher::where('teacher_id', $t->id)->pluck('book_id'))->unique();
                $profile->update([
                    'total_courses' => $bookIds->count(),
                    'total_students' => CourseEnrollment::whereIn('book_id', $bookIds)->pluck('user_id')->unique()->count(),
                ]);
            }
        }

        $this->command->info('Dummy data seeded: grades, subjects, users, teacher profiles, books, modules, chapters, lessons, topics, payments, enrollments, lesson progress, and course teachers.');
    }
}
