@extends('layouts.app')

@section('title', 'Course: ' . $course->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold">{{ $course->title }}</h1>
            <p class="text-gray-600 mt-1">{{ $course->subject->grade->name }} → {{ $course->subject->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('teacher.courses.edit', $course->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Edit Course
            </a>
            <a href="{{ route('teacher.courses.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                Back to Courses
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Course Info -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <span class="text-sm text-gray-500">Status</span>
                <p class="font-semibold">
                    <span class="px-2 py-1 rounded {{ $course->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($course->status) }}
                    </span>
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Price</span>
                <p class="font-semibold">
                    @if($course->is_free)
                        <span class="text-green-600">Free</span>
                    @else
                        Rs. {{ number_format($course->price, 0) }}
                    @endif
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-500">Duration</span>
                <p class="font-semibold">{{ $course->duration_months }} months</p>
            </div>
        </div>
    </div>

    <!-- Chapters -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold">Chapters & Lessons</h2>
            <button onclick="document.getElementById('addChapterModal').classList.remove('hidden')"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Add Chapter
            </button>
        </div>

        <div class="space-y-6">
            @forelse($course->chapters as $chapter)
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <h3 class="text-lg font-semibold">{{ $chapter->title }}</h3>
                            @if($chapter->is_free)
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">FREE</span>
                            @else
                                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">PAID</span>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <button onclick="editChapter({{ $chapter->id }}, '{{ $chapter->title }}', '{{ $chapter->description }}', {{ $chapter->is_free ? 'true' : 'false' }}, {{ $chapter->order }})"
                                    class="text-blue-600 hover:text-blue-700 text-sm">Edit</button>
                            <form action="{{ route('teacher.courses.chapters.destroy', ['bookId' => $course->id, 'chapterId' => $chapter->id]) }}"
                                  method="POST" onsubmit="return confirm('Are you sure? This will delete all lessons in this chapter.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 text-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                    @if($chapter->description)
                        <p class="text-sm text-gray-600 mb-3">{{ $chapter->description }}</p>
                    @endif

                    <!-- Lessons in Chapter -->
                    <div class="ml-4 space-y-2">
                        @forelse($chapter->lessons as $lesson)
                            <div class="border-l-2 border-gray-200 pl-4 py-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium">{{ $lesson->title }}</span>
                                        @if($lesson->is_free)
                                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">FREE</span>
                                        @else
                                            <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">PAID</span>
                                        @endif
                                        <span class="text-xs text-gray-500">{{ ucfirst($lesson->status) }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('teacher.lessons.edit', $lesson->id) }}" class="text-blue-600 hover:text-blue-700 text-sm">Edit</a>
                                        <form action="{{ route('teacher.courses.chapters.lessons.destroy', ['bookId' => $course->id, 'chapterId' => $chapter->id, 'lessonId' => $lesson->id]) }}"
                                              method="POST" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 italic">No lessons yet</p>
                        @endforelse
                        <button onclick="showAddLessonModal({{ $chapter->id }})"
                                class="text-sm text-blue-600 hover:text-blue-700 mt-2">
                            + Add Lesson
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">No chapters yet. Add your first chapter to get started.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Add Chapter Modal -->
<div id="addChapterModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold mb-4">Add Chapter</h3>
        <form action="{{ route('teacher.courses.chapters.store', $course->id) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                    <input type="number" name="order" value="0" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_free" id="chapter_is_free" class="h-4 w-4 text-blue-600">
                    <label for="chapter_is_free" class="ml-2 text-sm text-gray-700">
                        Mark as FREE (students can access without purchase)
                    </label>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Create Chapter
                    </button>
                    <button type="button" onclick="document.getElementById('addChapterModal').classList.add('hidden')"
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Chapter Modal -->
<div id="editChapterModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold mb-4">Edit Chapter</h3>
        <form id="editChapterForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" id="edit_chapter_title" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="edit_chapter_description" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                    <input type="number" name="order" id="edit_chapter_order" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_free" id="edit_chapter_is_free" class="h-4 w-4 text-blue-600">
                    <label for="edit_chapter_is_free" class="ml-2 text-sm text-gray-700">
                        Mark as FREE (students can access without purchase)
                    </label>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Update Chapter
                    </button>
                    <button type="button" onclick="document.getElementById('editChapterModal').classList.add('hidden')"
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Add Lesson Modal -->
<div id="addLessonModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold mb-4">Add Lesson</h3>
        <form id="addLessonForm" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                    <input type="number" name="order" value="0" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_free" id="lesson_is_free" class="h-4 w-4 text-blue-600">
                    <label for="lesson_is_free" class="ml-2 text-sm text-gray-700">
                        Mark as FREE (students can access without purchase)
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border rounded-lg">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Create Lesson
                    </button>
                    <button type="button" onclick="document.getElementById('addLessonModal').classList.add('hidden')"
                            class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editChapter(id, title, description, isFree, order) {
    document.getElementById('edit_chapter_title').value = title;
    document.getElementById('edit_chapter_description').value = description || '';
    document.getElementById('edit_chapter_order').value = order;
    document.getElementById('edit_chapter_is_free').checked = isFree;
    document.getElementById('editChapterForm').action = '{{ route("teacher.courses.chapters.update", ["bookId" => $course->id, "chapterId" => ":id"]) }}'.replace(':id', id);
    document.getElementById('editChapterModal').classList.remove('hidden');
}

function showAddLessonModal(chapterId) {
    document.getElementById('addLessonForm').action = '{{ route("teacher.courses.chapters.lessons.store", ["bookId" => $course->id, "chapterId" => ":id"]) }}'.replace(':id', chapterId);
    document.getElementById('addLessonModal').classList.remove('hidden');
}
</script>
@endpush
@endsection

