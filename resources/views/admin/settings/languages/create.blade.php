@extends('layouts.admin')

@section('title', 'Add Language')
@section('page-title', 'Add New Language')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('admin.settings.languages.store') }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Language Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="e.g., English, Urdu, Arabic"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Language Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}" required maxlength="10"
                           placeholder="e.g., en, ur, ar"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('code') border-red-500 @enderror">
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">ISO 639-1 code (2-3 letters)</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Native Name</label>
                <input type="text" name="native_name" value="{{ old('native_name') }}"
                       placeholder="Native name of the language"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">How the language is written in its own script</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Flag Emoji</label>
                    <input type="text" name="flag" value="{{ old('flag') }}" maxlength="10"
                           placeholder="🇬🇧, 🇵🇰, 🇸🇦"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-2xl">
                    <p class="text-xs text-gray-500 mt-1">Flag emoji or country code</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Text Direction <span class="text-red-500">*</span>
                    </label>
                    <select name="direction" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="ltr" {{ old('direction', 'ltr') == 'ltr' ? 'selected' : '' }}>Left to Right (LTR)</option>
                        <option value="rtl" {{ old('direction') == 'rtl' ? 'selected' : '' }}>Right to Left (RTL)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="2"
                          placeholder="Optional description..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                           class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Active</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="is_default" value="1" 
                           class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500" {{ old('is_default') ? 'checked' : '' }}>
                    <label for="is_default" class="ml-2 text-sm font-medium text-gray-700">Set as Default Language</label>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Create Language
                </button>
                <a href="{{ route('admin.settings.languages.index') }}" 
                   class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 text-center">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
