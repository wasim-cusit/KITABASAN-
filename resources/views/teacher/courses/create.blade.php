@extends('layouts.teacher')

@section('title', 'Create Course')
@section('page-title', 'Create New Course')

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <form action="{{ route('teacher.courses.store') }}" method="POST" enctype="multipart/form-data" id="courseForm">
                @csrf

                <div class="space-y-6">
                    <!-- Basic Information -->
                    <div class="border-b pb-4">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Grade Input (Text) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Grade <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="grade_name" id="grade_name" required 
                                       value="{{ old('grade_name') }}"
                                       placeholder="Enter Grade (e.g., Grade 10, Class 12)"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('grade_name') border-red-500 @enderror">
                                @error('grade_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Enter the grade/class level</p>
                            </div>

                            <!-- Subject Input (Text) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="subject_name" id="subject_name" required 
                                       value="{{ old('subject_name') }}"
                                       placeholder="Enter Subject (e.g., Mathematics, English)"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('subject_name') border-red-500 @enderror">
                                @error('subject_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Enter the subject name</p>
                            </div>
                        </div>
                    </div>

                    <!-- Course Details -->
                    <div class="border-b pb-4">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Course Details</h2>
                        
                        <!-- Course Title -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Course Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                   placeholder="Enter course title"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Short Description -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Short Description <span class="text-gray-400 text-xs">(For course card)</span>
                            </label>
                            <textarea name="short_description" rows="2" maxlength="200"
                                      placeholder="Brief description (max 200 characters)"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('short_description') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1"><span id="short_desc_count">0</span>/200 characters</p>
                        </div>

                        <!-- Full Description -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="5" 
                                      placeholder="Detailed course description"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <!-- Media Section -->
                    <div class="border-b pb-4">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Media</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Thumbnail -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Course Thumbnail</label>
                                <input type="file" name="thumbnail" accept="image/*"
                                       onchange="previewImage(this, 'thumbnail_preview')"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <div id="thumbnail_preview" class="mt-2"></div>
                                <p class="text-xs text-gray-500 mt-1">Recommended: 400x300px, max 2MB</p>
                            </div>

                            <!-- Cover Image -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                                <input type="file" name="cover_image" accept="image/*"
                                       onchange="previewImage(this, 'cover_preview')"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <div id="cover_preview" class="mt-2"></div>
                                <p class="text-xs text-gray-500 mt-1">Recommended: 1200x675px, max 2MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Access -->
                    <div class="border-b pb-4">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Pricing & Access</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Price -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price (PKR)</label>
                                <input type="number" name="price" id="price_input" value="{{ old('price', 0) }}" min="0" step="0.01"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Access Duration -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Access Duration (Months)</label>
                                <input type="number" name="access_duration_months" value="{{ old('access_duration_months', 12) }}" min="1"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Free Course Checkbox -->
                        <div class="mt-4 flex items-center">
                            <input type="checkbox" name="is_free" id="is_free" value="1" class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500" 
                                   onchange="togglePriceField()" {{ old('is_free') ? 'checked' : '' }}>
                            <label for="is_free" class="ml-2 text-sm font-medium text-gray-700">This is a free course</label>
                        </div>
                    </div>

                    <!-- Course Classification -->
                    <div class="pb-4">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Course Classification</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Language -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                                <select name="language" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="en" {{ old('language', 'en') == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="ur" {{ old('language') == 'ur' ? 'selected' : '' }}>Urdu</option>
                                    <option value="ar" {{ old('language') == 'ar' ? 'selected' : '' }}>Arabic</option>
                                    <option value="other" {{ old('language') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <!-- Difficulty Level -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty Level</label>
                                <select name="difficulty_level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="all" {{ old('difficulty_level', 'all') == 'all' ? 'selected' : '' }}>All Levels</option>
                                    <option value="beginner" {{ old('difficulty_level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                    <option value="intermediate" {{ old('difficulty_level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                    <option value="advanced" {{ old('difficulty_level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                            Create Course
                        </button>
                        <a href="{{ route('teacher.courses.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 text-center font-semibold focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
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
// Toggle price field based on free checkbox
function togglePriceField() {
    const isFree = document.getElementById('is_free').checked;
    const priceInput = document.getElementById('price_input');
    if (isFree) {
        priceInput.value = 0;
        priceInput.disabled = true;
        priceInput.classList.add('bg-gray-100', 'cursor-not-allowed');
    } else {
        priceInput.disabled = false;
        priceInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
    }
}

// Preview image before upload
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="h-32 w-32 object-cover rounded-lg border">`;
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '';
    }
}

// Character counter for short description
document.addEventListener('DOMContentLoaded', function() {
    const shortDesc = document.querySelector('textarea[name="short_description"]');
    if (shortDesc) {
        // Initialize counter
        document.getElementById('short_desc_count').textContent = shortDesc.value.length;
        
        // Update counter on input
        shortDesc.addEventListener('input', function() {
            const count = this.value.length;
            document.getElementById('short_desc_count').textContent = count;
        });
    }
    
    // Initialize price field
    togglePriceField();
});
</script>
@endpush
@endsection
