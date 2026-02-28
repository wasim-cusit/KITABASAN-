@extends('layouts.admin')

@section('title', 'Edit Payment Method')
@section('page-title', 'Edit Payment Method: ' . $paymentMethod->name)

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-4xl">
    <form action="{{ route('admin.settings.payment-methods.update', $paymentMethod->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Payment Method Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $paymentMethod->name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code', $paymentMethod->code) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('code') border-red-500 @enderror">
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="2"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $paymentMethod->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Icon</label>
                @if($paymentMethod->icon)
                    <div class="mb-2">
                        <img src="{{ route('storage.serve', ['path' => ltrim(str_replace('\\', '/', $paymentMethod->icon), '/')]) }}" alt="{{ $paymentMethod->name }}" class="h-16 w-16 object-contain">
                    </div>
                @endif
                <input type="file" name="icon" accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Upload new icon to replace existing one</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Fee (%)</label>
                    <input type="number" name="transaction_fee_percentage" value="{{ old('transaction_fee_percentage', $paymentMethod->transaction_fee_percentage) }}"
                           min="0" max="100" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fixed Transaction Fee</label>
                    <input type="number" name="transaction_fee_fixed" value="{{ old('transaction_fee_fixed', $paymentMethod->transaction_fee_fixed) }}"
                           min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-700 mb-3">Credentials</h3>
                <div id="credentials-container" class="space-y-3">
                    @php
                        $credentials = $paymentMethod->credentials ?? [];
                        $credIndex = 1;
                    @endphp
                    @foreach($credentials as $key => $value)
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" name="credentials[key{{ $credIndex }}]" value="{{ $key }}" placeholder="Key"
                                   class="px-4 py-2 border border-gray-300 rounded-lg">
                            <input type="text" name="credentials[value{{ $credIndex }}]" value="{{ $value }}" placeholder="Value"
                                   class="px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        @php $credIndex++; @endphp
                    @endforeach
                    @if(empty($credentials))
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" name="credentials[key1]" placeholder="Key"
                                   class="px-4 py-2 border border-gray-300 rounded-lg">
                            <input type="text" name="credentials[value1]" placeholder="Value"
                                   class="px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    @endif
                </div>
                <button type="button" onclick="addCredentialField()"
                        class="mt-2 text-sm text-blue-600 hover:text-blue-800">+ Add More Credentials</button>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-700 mb-3">Configuration</h3>
                <div id="config-container" class="space-y-3">
                    @php
                        $config = $paymentMethod->config ?? [];
                        $configIndex = 1;
                    @endphp
                    @foreach($config as $key => $value)
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" name="config[key{{ $configIndex }}]" value="{{ $key }}" placeholder="Key"
                                   class="px-4 py-2 border border-gray-300 rounded-lg">
                            <input type="text" name="config[value{{ $configIndex }}]" value="{{ $value }}" placeholder="Value"
                                   class="px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        @php $configIndex++; @endphp
                    @endforeach
                    @if(empty($config))
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" name="config[key1]" placeholder="Key"
                                   class="px-4 py-2 border border-gray-300 rounded-lg">
                            <input type="text" name="config[value1]" placeholder="Value"
                                   class="px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    @endif
                </div>
                <button type="button" onclick="addConfigField()"
                        class="mt-2 text-sm text-blue-600 hover:text-blue-800">+ Add More Config</button>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Setup Instructions</label>
                <textarea name="instructions" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('instructions', $paymentMethod->instructions) }}</textarea>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500" {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Active</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_sandbox" id="is_sandbox" value="1"
                           class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500" {{ old('is_sandbox', $paymentMethod->is_sandbox) ? 'checked' : '' }}>
                    <label for="is_sandbox" class="ml-2 text-sm font-medium text-gray-700">Sandbox/Test Mode</label>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Update Payment Method
                </button>
                <a href="{{ route('admin.settings.payment-methods.index') }}"
                   class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 text-center">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let credentialCount = {{ count($paymentMethod->credentials ?? []) }} + 1;
let configCount = {{ count($paymentMethod->config ?? []) }} + 1;

function addCredentialField() {
    const container = document.getElementById('credentials-container');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-2 gap-4';
    div.innerHTML = `
        <input type="text" name="credentials[key${credentialCount}]" placeholder="Key"
               class="px-4 py-2 border border-gray-300 rounded-lg">
        <input type="text" name="credentials[value${credentialCount}]" placeholder="Value"
               class="px-4 py-2 border border-gray-300 rounded-lg">
    `;
    container.appendChild(div);
    credentialCount++;
}

function addConfigField() {
    const container = document.getElementById('config-container');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-2 gap-4';
    div.innerHTML = `
        <input type="text" name="config[key${configCount}]" placeholder="Key"
               class="px-4 py-2 border border-gray-300 rounded-lg">
        <input type="text" name="config[value${configCount}]" placeholder="Value"
               class="px-4 py-2 border border-gray-300 rounded-lg">
    `;
    container.appendChild(div);
    configCount++;
}
</script>
@endpush
@endsection
