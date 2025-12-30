@extends('layouts.admin')

@section('title', 'Create Course')
@section('page-title', 'Create New Course')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-3xl">
    <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Grade</label>
                <select name="grade_id" id="grade_id" class="w-full px-4 py-2 border rounded-lg" onchange="loadSubjects()">
                    <option value="">Select Grade</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                <select name="subject_id" id="subject_id" required class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Select Subject</option>
                </select>
                @error('subject_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Teacher</label>
                <select name="teacher_id" required class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Select Teacher</option>
                    @foreach(\App\Models\User::role('teacher')->get() as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
                @error('teacher_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Course Title</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full px-4 py-2 border rounded-lg @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="5" 
                          class="w-full px-4 py-2 border rounded-lg @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                <input type="file" name="cover_image" accept="image/*"
                       class="w-full px-4 py-2 border rounded-lg">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price (PKR)</label>
                    <input type="number" name="price" value="{{ old('price', 0) }}" min="0" step="0.01"
                           class="w-full px-4 py-2 border rounded-lg" id="price_input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Months)</label>
                    <input type="number" name="duration_months" value="{{ old('duration_months', 12) }}" min="1"
                           class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_free" id="is_free" value="1" class="h-4 w-4 text-blue-600" 
                       onchange="togglePriceField()">
                <label for="is_free" class="ml-2 text-sm text-gray-700">This is a free course</label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" required class="w-full px-4 py-2 border rounded-lg">
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Create Course
                </button>
                <a href="{{ route('admin.courses.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 text-center">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
const grades = @json($grades);

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

