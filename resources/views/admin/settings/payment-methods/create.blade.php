@extends('layouts.admin')

@section('title', 'Add Payment Method')
@section('page-title', 'Add New Payment Method')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-4xl">
    <form action="{{ route('admin.settings.payment-methods.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Payment Method Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="e.g., Stripe, PayPal, JazzCash"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}" required
                           placeholder="e.g., stripe, paypal, jazzcash"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('code') border-red-500 @enderror">
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Unique identifier (lowercase, no spaces)</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="2"
                          placeholder="Brief description of this payment method"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Icon</label>
                <input type="file" name="icon" accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Upload payment method icon/logo (optional)</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Fee (%)</label>
                    <input type="number" name="transaction_fee_percentage" value="{{ old('transaction_fee_percentage', 0) }}" 
                           min="0" max="100" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fixed Transaction Fee</label>
                    <input type="number" name="transaction_fee_fixed" value="{{ old('transaction_fee_fixed', 0) }}" 
                           min="0" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-700 mb-3">Credentials</h3>
                <div id="credentials-container" class="space-y-3">
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="credentials[key1]" placeholder="Key (e.g., merchant_id)" 
                               class="px-4 py-2 border border-gray-300 rounded-lg">
                        <input type="text" name="credentials[value1]" placeholder="Value" 
                               class="px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="credentials[key2]" placeholder="Key (e.g., password)" 
                               class="px-4 py-2 border border-gray-300 rounded-lg">
                        <input type="text" name="credentials[value2]" placeholder="Value" 
                               class="px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <button type="button" onclick="addCredentialField()" 
                        class="mt-2 text-sm text-blue-600 hover:text-blue-800">+ Add More Credentials</button>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-700 mb-3">Configuration</h3>
                <div id="config-container" class="space-y-3">
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="config[key1]" placeholder="Key (e.g., sandbox_url)" 
                               class="px-4 py-2 border border-gray-300 rounded-lg">
                        <input type="text" name="config[value1]" placeholder="Value" 
                               class="px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <button type="button" onclick="addConfigField()" 
                        class="mt-2 text-sm text-blue-600 hover:text-blue-800">+ Add More Config</button>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Setup Instructions</label>
                <textarea name="instructions" rows="4"
                          placeholder="Instructions for setting up this payment method..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('instructions') }}</textarea>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                           class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Active</label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_sandbox" id="is_sandbox" value="1" 
                           class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500" {{ old('is_sandbox', true) ? 'checked' : '' }}>
                    <label for="is_sandbox" class="ml-2 text-sm font-medium text-gray-700">Sandbox/Test Mode</label>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Create Payment Method
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
let credentialCount = 3;
let configCount = 2;

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
