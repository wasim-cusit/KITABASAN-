@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">System Settings</h1>

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold mb-4">General Settings</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                            <input type="text" name="site_name" value="{{ old('site_name') }}"
                                   class="w-full px-3 py-2 border rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Site Email</label>
                            <input type="email" name="site_email" value="{{ old('site_email') }}"
                                   class="w-full px-3 py-2 border rounded">
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold mb-4">Payment Settings</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">JazzCash Merchant ID</label>
                            <input type="text" name="jazzcash_merchant_id" value="{{ old('jazzcash_merchant_id') }}"
                                   class="w-full px-3 py-2 border rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">EasyPaisa Merchant ID</label>
                            <input type="text" name="easypaisa_merchant_id" value="{{ old('easypaisa_merchant_id') }}"
                                   class="w-full px-3 py-2 border rounded">
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold mb-4">Video Settings</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">YouTube API Key</label>
                            <input type="text" name="youtube_api_key" value="{{ old('youtube_api_key') }}"
                                   class="w-full px-3 py-2 border rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bunny Stream API Key</label>
                            <input type="text" name="bunny_api_key" value="{{ old('bunny_api_key') }}"
                                   class="w-full px-3 py-2 border rounded">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

