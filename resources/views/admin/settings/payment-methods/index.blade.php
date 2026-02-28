@extends('layouts.admin')

@section('title', 'Payment Methods')
@section('page-title', 'Payment Methods Management')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Payment Methods</h2>
            <a href="{{ route('admin.settings.payment-methods.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Add New Payment Method
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Icon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction Fee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($paymentMethods as $method)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($method->icon)
                                    <img src="{{ route('storage.serve', ['path' => ltrim(str_replace('\\', '/', $method->icon), '/')]) }}" alt="{{ $method->name }}" class="h-10 w-10 object-contain">
                                @else
                                    <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center text-gray-400">
                                        💳
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $method->name }}</div>
                                @if($method->description)
                                    <div class="text-sm text-gray-500">{{ \Str::limit($method->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <code class="bg-gray-100 px-2 py-1 rounded">{{ $method->code }}</code>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full {{ $method->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $method->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 py-1 text-xs rounded {{ $method->is_sandbox ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $method->is_sandbox ? 'Sandbox' : 'Production' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($method->transaction_fee_percentage > 0 || $method->transaction_fee_fixed > 0)
                                    {{ number_format($method->transaction_fee_percentage, 2) }}%
                                    @if($method->transaction_fee_fixed > 0)
                                        + {{ number_format($method->transaction_fee_fixed, 2) }}
                                    @endif
                                @else
                                    <span class="text-gray-400">Free</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.settings.payment-methods.edit', $method->id) }}"
                                       class="text-blue-600 hover:text-blue-900">Edit</a>
                                    <form action="{{ route('admin.settings.payment-methods.toggle-status', $method->id) }}"
                                          method="POST" class="inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit"
                                                class="text-{{ $method->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $method->is_active ? 'yellow' : 'green' }}-900">
                                            {{ $method->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.settings.payment-methods.destroy', $method->id) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this payment method?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <p class="mb-2">No payment methods found.</p>
                                <a href="{{ route('admin.settings.payment-methods.create') }}" class="text-blue-600 hover:text-blue-800">Add your first payment method</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
