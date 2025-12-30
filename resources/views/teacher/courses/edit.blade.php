@extends('layouts.app')

@section('title', 'Edit Course')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Edit Course</h1>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('teacher.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Grade</label>
                        <select name="grade_id" id="grade_id" class="w-full px-4 py-2 border rounded-lg" onchange="loadSubjects()">
                            <option value="">Select Grade</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}" {{ $course->subject->grade_id == $grade->id ? 'selected' : '' }}>
                                    {{ $grade->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <select name="subject_id" id="subject_id" class="w-full px-4 py-2 border rounded-lg" required>
                            <option value="">Select Subject</option>
                        </select>
                        @error('subject_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Course Title</label>
                        <input type="text" name="title" value="{{ old('title', $course->title) }}" required
                               class="w-full px-4 py-2 border rounded-lg @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="5"
                                  class="w-full px-4 py-2 border rounded-lg @error('description') border-red-500 @enderror">{{ old('description', $course->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                        @if($course->cover_image)
                            <div class="mb-2">
                                <img src="{{ \Storage::url($course->cover_image) }}" alt="Current cover" class="h-32 rounded">
                            </div>
                        @endif
                        <input type="file" name="cover_image" accept="image/*"
                               class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price (PKR)</label>
                            <input type="number" name="price" value="{{ old('price', $course->price) }}" min="0" step="0.01"
                                   class="w-full px-4 py-2 border rounded-lg" id="price_input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Months)</label>
                            <input type="number" name="duration_months" value="{{ old('duration_months', $course->duration_months) }}" min="1"
                                   class="w-full px-4 py-2 border rounded-lg">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_free" id="is_free" value="1" class="h-4 w-4 text-blue-600"
                               {{ $course->is_free ? 'checked' : '' }} onchange="togglePriceField()">
                        <label for="is_free" class="ml-2 text-sm text-gray-700">
                            This is a free course
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-2 border rounded-lg">
                            <option value="draft" {{ $course->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ $course->status === 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                            Update Course
                        </button>
                        <a href="{{ route('teacher.courses.show', $course->id) }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 text-center font-semibold">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const grades = @json($grades);
const currentSubjectId = {{ $course->subject_id }};

window.onload = function() {
    loadSubjects();
    document.getElementById('subject_id').value = currentSubjectId;
    togglePriceField();
};

function loadSubjects() {
    const gradeId = document.getElementById('grade_id').value;
    const subjectSelect = document.getElementById('subject_id');
    subjectSelect.innerHTML = '<option value="">Select Subject</option>';

    if (gradeId) {
        const grade = grades.find(g => g.id == gradeId);
        if (grade && grade.subjects) {
            grade.subjects.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.id;
                option.textContent = subject.name;
                if (subject.id == currentSubjectId) {
                    option.selected = true;
                }
                subjectSelect.appendChild(option);
            });
        }
    }
}

function togglePriceField() {
    const isFree = document.getElementById('is_free').checked;
    const priceInput = document.getElementById('price_input');
    if (isFree) {
        priceInput.value = 0;
        priceInput.disabled = true;
    } else {
        priceInput.disabled = false;
    }
}
</script>
@endpush
@endsection

