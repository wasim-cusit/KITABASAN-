@extends('layouts.admin')

@section('title', 'Create Course')
@section('page-title', 'Create New Course')

@section('content')
<div class="bg-white rounded-lg shadow">
    <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data" id="courseForm">
        @csrf

        <div class="p-6 space-y-6">
            <!-- Basic Information Section -->
            <div class="border-b pb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Basic Information</h2>

                <div class="space-y-4">
                    <!-- Grade, Subject, and Language in one row -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Grade Input (Text) -->
                        <div class="flex-1">
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
                        <div class="flex-1">
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

                        <!-- Language -->
                        <div class="w-full sm:w-48">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                            <select name="language" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="en" {{ old('language', 'en') == 'en' ? 'selected' : '' }}>English</option>
                                <option value="ur" {{ old('language') == 'ur' ? 'selected' : '' }}>Urdu</option>
                                <option value="ar" {{ old('language') == 'ar' ? 'selected' : '' }}>Arabic</option>
                                <option value="other" {{ old('language') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Select language</p>
                        </div>
                    </div>

                    <!-- Main Teacher and Additional Teachers in one row -->
                    <div class="flex flex-col lg:flex-row gap-4">
                        <!-- Main Teacher Selection -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Main Teacher (Creator) <span class="text-red-500">*</span>
                            </label>
                            <select name="main_teacher_id" id="main_teacher_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('main_teacher_id') border-red-500 @enderror">
                                <option value="">Select Main Teacher</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('main_teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }} ({{ $teacher->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('main_teacher_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Select the primary teacher/creator of this course</p>
                        </div>

                        <!-- Multiple Teachers Selection -->
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Additional Teachers (Co-Teachers) <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <div class="border border-gray-300 rounded-lg p-4 max-h-48 overflow-y-auto bg-gray-50">
                                @if(count($teachers) > 0)
                                    @foreach($teachers as $teacher)
                                        <div class="flex items-center mb-2 p-2 hover:bg-gray-100 rounded">
                                            <input type="checkbox" name="teacher_ids[]" id="teacher_{{ $teacher->id }}"
                                                   value="{{ $teacher->id }}"
                                                   {{ old('teacher_ids') && is_array(old('teacher_ids')) && in_array($teacher->id, old('teacher_ids')) ? 'checked' : '' }}
                                                   class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500">
                                            <label for="teacher_{{ $teacher->id }}" class="ml-2 text-sm text-gray-700 cursor-pointer flex-1">
                                                {{ $teacher->name }} ({{ $teacher->email }})
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-sm text-gray-500 text-center py-4">No teachers available</p>
                                @endif
                            </div>
                            @error('teacher_ids')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            @error('teacher_ids.*')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Select additional teachers to assign to this course (they will be co-teachers). The main teacher is automatically included.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Details Section -->
            <div class="border-b pb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Course Details</h2>

                <!-- Course Title -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Course / Book Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           placeholder="Enter course or book title"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Enter a clear and descriptive title for your course or book</p>
                </div>

                <!-- Short Description -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Short Description <span class="text-gray-400 text-xs">(For course card preview)</span>
                    </label>
                    <textarea name="short_description" rows="2" maxlength="200"
                              placeholder="Brief description that appears on course cards (max 200 characters)"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('short_description') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1"><span id="short_desc_count">0</span>/200 characters</p>
                </div>

                <!-- Full Description -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Description</label>
                    <textarea name="description" id="description" rows="6"
                              placeholder="Detailed course description"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                </div>

                <!-- What You Will Learn -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">What You Will Learn</label>
                    <textarea name="what_you_will_learn" rows="4"
                              placeholder="Enter key learning points (one per line or bullet points)"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('what_you_will_learn') }}</textarea>
                </div>

                <!-- Course Requirements -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Course Requirements</label>
                    <textarea name="course_requirements" rows="3"
                              placeholder="Prerequisites or requirements for this course"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('course_requirements') }}</textarea>
                </div>

                <!-- Target Audience -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
                    <textarea name="target_audience" rows="2"
                              placeholder="Who is this course for?"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('target_audience') }}</textarea>
                </div>
            </div>

            <!-- Course Classification Section -->
            <div class="border-b pb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Course Classification</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Difficulty Level -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Level / Difficulty <span class="text-red-500">*</span>
                        </label>
                        <select name="difficulty_level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('difficulty_level') border-red-500 @enderror">
                            <option value="beginner" {{ old('difficulty_level', 'beginner') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ old('difficulty_level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ old('difficulty_level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                            <option value="all" {{ old('difficulty_level') == 'all' ? 'selected' : '' }}>All Levels</option>
                        </select>
                        @error('difficulty_level')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Select the difficulty level</p>
                    </div>

                    <!-- Course Level -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Education Level</label>
                        <select name="course_level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Education Level</option>
                            <option value="elementary" {{ old('course_level') == 'elementary' ? 'selected' : '' }}>Elementary</option>
                            <option value="secondary" {{ old('course_level') == 'secondary' ? 'selected' : '' }}>Secondary</option>
                            <option value="higher_secondary" {{ old('course_level') == 'higher_secondary' ? 'selected' : '' }}>Higher Secondary</option>
                            <option value="undergraduate" {{ old('course_level') == 'undergraduate' ? 'selected' : '' }}>Undergraduate</option>
                            <option value="graduate" {{ old('course_level') == 'graduate' ? 'selected' : '' }}>Graduate</option>
                            <option value="professional" {{ old('course_level') == 'professional' ? 'selected' : '' }}>Professional</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Optional: Education level</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Course Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft (Save for later)</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published (Make it live)</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Choose when to make course available</p>
                    </div>
                </div>

                <!-- Tags -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                    <input type="text" name="tags_input" id="tags_input"
                           placeholder="Enter tags separated by commas (e.g., web development, javascript, react)"
                           value="{{ old('tags_input') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Separate multiple tags with commas</p>
                    <input type="hidden" name="tags" id="tags_hidden">
                </div>
            </div>

            <!-- Media Section -->
            <div class="border-b pb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Media</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Thumbnail -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Course Thumbnail</label>
                        <input type="file" name="thumbnail" accept="image/*"
                               onchange="previewImage(this, 'thumbnail_preview')"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div id="thumbnail_preview" class="mt-2"></div>
                        <p class="text-xs text-gray-500 mt-1">Recommended: 400x300px, max 10MB</p>
                    </div>

                    <!-- Cover Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                        <input type="file" name="cover_image" accept="image/*"
                               onchange="previewImage(this, 'cover_preview')"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div id="cover_preview" class="mt-2"></div>
                        <p class="text-xs text-gray-500 mt-1">Recommended: 1200x675px, max 10MB</p>
                    </div>
                </div>

                <!-- Intro Video -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Intro Video (Optional)</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div></div>
                            <select name="intro_video_provider" id="intro_video_provider"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    onchange="toggleIntroVideoInput()">
                                <option value="">Select Video Provider</option>
                                <option value="youtube" {{ old('intro_video_provider') == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                <option value="vimeo" {{ old('intro_video_provider') == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                                <option value="upload" {{ old('intro_video_provider') == 'upload' ? 'selected' : '' }}>Upload Video</option>
                                <option value="bunny" {{ old('intro_video_provider') == 'bunny' ? 'selected' : '' }}>Bunny Stream</option>
                            </select>
                        </div>
                        <div id="intro_video_input_container" class="hidden">
                            <input type="text" name="intro_video_url" id="intro_video_url"
                                   value="{{ old('intro_video_url') }}"
                                   placeholder="Enter video ID or URL"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div id="intro_video_file_container" class="hidden mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Video File</label>
                        <input type="file" name="intro_video_file" id="intro_video_file" accept="video/*"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Max file size: 100MB (mp4, avi, mov)</p>
                    </div>
                </div>
            </div>

            <!-- Pricing & Access Section -->
            <div class="border-b pb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Pricing & Access Control</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Price (PKR) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price" id="price_input" value="{{ old('price', 0) }}" min="0" step="0.01"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price') border-red-500 @enderror">
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Set price for paid courses</p>
                    </div>

                    <!-- Access Validity -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Access Validity <span class="text-red-500">*</span>
                        </label>
                        <select name="validity_type" id="validity_type"
                                onchange="toggleValidityCustom()"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('access_duration_months') border-red-500 @enderror">
                            <option value="lifetime" {{ old('validity_type', 'lifetime') == 'lifetime' ? 'selected' : '' }}>Lifetime Access</option>
                            <option value="3_months" {{ old('validity_type') == '3_months' ? 'selected' : '' }}>3 Months</option>
                            <option value="6_months" {{ old('validity_type') == '6_months' ? 'selected' : '' }}>6 Months</option>
                            <option value="custom" {{ old('validity_type') == 'custom' ? 'selected' : '' }}>Custom Duration</option>
                        </select>
                        <input type="number" name="access_duration_months" id="access_duration_months"
                               value="{{ old('access_duration_months') }}" min="1"
                               placeholder="Enter months"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mt-2 hidden">
                        @error('access_duration_months')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">How long students can access this course</p>
                    </div>

                    <!-- Max Enrollments -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Enrollments</label>
                        <input type="number" name="max_enrollments" value="{{ old('max_enrollments') }}" min="1"
                               placeholder="Leave empty for unlimited"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Leave empty for unlimited enrollments</p>
                    </div>
                </div>

                <!-- Free Course Checkbox -->
                <div class="mt-4 flex items-center">
                    <input type="checkbox" name="is_free" id="is_free" value="1" class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500"
                           onchange="togglePriceField()" {{ old('is_free') ? 'checked' : '' }}>
                    <label for="is_free" class="ml-2 text-sm font-medium text-gray-700">This is a free course (Price will be set to 0)</label>
                </div>
            </div>

            <!-- Course Schedule Section -->
            <div class="border-b pb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Course Schedule (Optional)</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Features & Settings Section -->
            <div class="border-b pb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Features & Settings</h2>

                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="checkbox" name="certificate_enabled" id="certificate_enabled" value="1"
                               class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500" {{ old('certificate_enabled') ? 'checked' : '' }}>
                        <label for="certificate_enabled" class="ml-2 text-sm font-medium text-gray-700">Enable Certificate of Completion</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="reviews_enabled" id="reviews_enabled" value="1"
                               class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500" {{ old('reviews_enabled', true) ? 'checked' : '' }}>
                        <label for="reviews_enabled" class="ml-2 text-sm font-medium text-gray-700">Enable Reviews & Ratings</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="comments_enabled" id="comments_enabled" value="1"
                               class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500" {{ old('comments_enabled', true) ? 'checked' : '' }}>
                        <label for="comments_enabled" class="ml-2 text-sm font-medium text-gray-700">Enable Comments</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1"
                               class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500" {{ old('is_featured') ? 'checked' : '' }}>
                        <label for="is_featured" class="ml-2 text-sm font-medium text-gray-700">Featured Course</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_popular" id="is_popular" value="1"
                               class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500" {{ old('is_popular') ? 'checked' : '' }}>
                        <label for="is_popular" class="ml-2 text-sm font-medium text-gray-700">Popular Course</label>
                    </div>
                </div>
            </div>

            <!-- SEO Section (Optional) -->
            <div class="pb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">SEO Settings (Optional)</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title') }}" maxlength="60"
                               placeholder="SEO meta title (max 60 characters)"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Recommended: 50-60 characters</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                        <textarea name="meta_description" rows="3" maxlength="160"
                                  placeholder="SEO meta description (max 160 characters)"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('meta_description') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Recommended: 150-160 characters</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                        <input type="text" name="meta_keywords_input" id="meta_keywords_input"
                               placeholder="Enter keywords separated by commas"
                               value="{{ old('meta_keywords_input') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <input type="hidden" name="meta_keywords" id="meta_keywords_hidden">
                        <p class="text-xs text-gray-500 mt-1">Separate keywords with commas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-gray-50 px-6 py-4 flex gap-4">
            <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                Create Course
            </button>
            <a href="{{ route('admin.courses.index') }}" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 text-center focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Ensure main teacher is included in teacher_ids array on form submit
document.getElementById('courseForm').addEventListener('submit', function(e) {
    const mainTeacherId = document.getElementById('main_teacher_id').value;
    if (!mainTeacherId) {
        e.preventDefault();
        alert('Please select a main teacher');
        return false;
    }

    // Ensure main teacher is checked in teacher_ids array (if checkbox exists)
    const mainTeacherCheckbox = document.getElementById('teacher_' + mainTeacherId);
    if (mainTeacherCheckbox && !mainTeacherCheckbox.checked) {
        mainTeacherCheckbox.checked = true;
    }

    // Convert tags input to JSON array
    const tagsInput = document.getElementById('tags_input');
    if (tagsInput && tagsInput.value) {
        const tags = tagsInput.value.split(',').map(tag => tag.trim()).filter(tag => tag !== '');
        document.getElementById('tags_hidden').value = JSON.stringify(tags);
    }

    // Convert meta keywords to JSON array
    const keywordsInput = document.getElementById('meta_keywords_input');
    if (keywordsInput && keywordsInput.value) {
        const keywords = keywordsInput.value.split(',').map(k => k.trim()).filter(k => k !== '');
        document.getElementById('meta_keywords_hidden').value = JSON.stringify(keywords);
    }
});
</script>
<script>

// Note: Grade and Subject are now text inputs, no AJAX loading needed

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

// Toggle validity custom input based on validity type
function toggleValidityCustom() {
    const validityType = document.getElementById('validity_type').value;
    const customInput = document.getElementById('access_duration_months');

    if (validityType === 'custom') {
        customInput.classList.remove('hidden');
        customInput.required = true;
    } else {
        customInput.classList.add('hidden');
        customInput.required = false;

        // Set value based on selection
        if (validityType === '3_months') {
            customInput.value = 3;
        } else if (validityType === '6_months') {
            customInput.value = 6;
        } else if (validityType === 'lifetime') {
            customInput.value = ''; // Lifetime = null
        }
    }
}

// Toggle intro video input based on provider
function toggleIntroVideoInput() {
    const provider = document.getElementById('intro_video_provider').value;
    const textContainer = document.getElementById('intro_video_input_container');
    const fileContainer = document.getElementById('intro_video_file_container');
    const textInput = document.getElementById('intro_video_url');

    if (provider === 'upload') {
        // Hide text input, show file input
        if (textContainer) textContainer.classList.add('hidden');
        if (fileContainer) fileContainer.classList.remove('hidden');
        if (textInput) textInput.value = '';
    } else {
        // Show text input, hide file input
        if (textContainer) textContainer.classList.remove('hidden');
        if (fileContainer) fileContainer.classList.add('hidden');

        if (textInput) {
            if (provider === 'youtube') {
                textInput.placeholder = 'Enter YouTube Video ID (e.g., dQw4w9WgXcQ)';
            } else if (provider === 'vimeo') {
                textInput.placeholder = 'Enter Vimeo Video ID';
            } else if (provider === 'bunny') {
                textInput.placeholder = 'Enter Bunny Stream Video ID';
            } else if (provider) {
                textInput.placeholder = 'Enter video ID or URL';
            } else {
                textInput.placeholder = 'Select provider first';
            }
        }
    }

    // Hide both if no provider selected
    if (!provider) {
        if (textContainer) textContainer.classList.add('hidden');
        if (fileContainer) fileContainer.classList.add('hidden');
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
document.querySelector('textarea[name="short_description"]').addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('short_desc_count').textContent = count;
});

// Process tags before form submission
document.getElementById('courseForm').addEventListener('submit', function(e) {
    // Convert tags input to JSON array
    const tagsInput = document.getElementById('tags_input').value;
    const tags = tagsInput.split(',').map(tag => tag.trim()).filter(tag => tag !== '');
    document.getElementById('tags_hidden').value = JSON.stringify(tags);

    // Convert meta keywords to JSON array
    const keywordsInput = document.getElementById('meta_keywords_input').value;
    const keywords = keywordsInput.split(',').map(k => k.trim()).filter(k => k !== '');
    document.getElementById('meta_keywords_hidden').value = JSON.stringify(keywords);
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize price field
    const priceField = document.getElementById('price_input');
    const isFreeCheckbox = document.getElementById('is_free');
    if (priceField && isFreeCheckbox) {
        togglePriceField();
    }

    // Initialize character counter for short description
    const shortDesc = document.querySelector('textarea[name="short_description"]');
    if (shortDesc) {
        const counter = document.getElementById('short_desc_count');
        if (counter) {
            counter.textContent = shortDesc.value.length;

            // Update counter on input
            shortDesc.addEventListener('input', function() {
                counter.textContent = this.value.length;
            });
        }
    }

    // Initialize intro video input
    const introVideoProvider = document.getElementById('intro_video_provider');
    if (introVideoProvider) {
        @if(old('intro_video_provider'))
            toggleIntroVideoInput();
        @else
            // Hide inputs initially if no provider selected
            toggleIntroVideoInput();
        @endif
    }

    // Initialize validity type
    const validityType = document.getElementById('validity_type');
    if (validityType) {
        toggleValidityCustom();
    }

    // Ensure main teacher checkbox is checked if pre-selected
    const mainTeacherSelect = document.getElementById('main_teacher_id');
    if (mainTeacherSelect) {
        const mainTeacherId = mainTeacherSelect.value;
        if (mainTeacherId) {
            const mainTeacherCheckbox = document.getElementById('teacher_' + mainTeacherId);
            if (mainTeacherCheckbox) {
                mainTeacherCheckbox.checked = true;
            }
        }

        // Auto-check main teacher when selected
        mainTeacherSelect.addEventListener('change', function() {
            const teacherId = this.value;
            if (teacherId) {
                const checkbox = document.getElementById('teacher_' + teacherId);
                if (checkbox) {
                    checkbox.checked = true;
                }
            }
        });
    }

    // Prevent unchecking main teacher if they're selected as main teacher
    document.querySelectorAll('input[name="teacher_ids[]"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const mainTeacherId = document.getElementById('main_teacher_id').value;
            if (mainTeacherId && this.value == mainTeacherId && !this.checked) {
                // Prevent unchecking if they're the main teacher
                this.checked = true;
                alert('The main teacher cannot be unchecked. Please change the main teacher first.');
            }
        });
    });
});
</script>
@endpush
@endsection
