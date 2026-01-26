@extends('layouts.admin')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-4 lg:p-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-6">
            <div>
                <h2 class="text-xl lg:text-2xl font-bold text-gray-900">Payment Details</h2>
                <p class="text-sm text-gray-500 mt-1">Review transaction information and update status.</p>
            </div>
            <a href="{{ route('admin.payments.index') }}"
               class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm font-semibold whitespace-nowrap">
                ← Back to Payments
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
        <div>
            <span class="text-sm text-gray-500">Transaction ID</span>
            <p class="font-mono font-semibold">{{ $payment->transaction_id ?? 'N/A' }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500">Status</span>
            <p>
                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                    {{ $payment->status == 'completed' ? 'bg-green-100 text-green-800' : 
                       ($payment->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                       ($payment->status == 'failed' ? 'bg-red-100 text-red-800' : 
                       ($payment->status == 'cancelled' ? 'bg-gray-100 text-gray-800' : 'bg-gray-100 text-gray-800'))) }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </p>
        </div>
        <div>
            <span class="text-sm text-gray-500">User</span>
            <p class="font-semibold">{{ $payment->user->name ?? 'N/A' }}</p>
            <p class="text-sm text-gray-600">{{ $payment->user->email ?? '' }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500">Course</span>
            <p class="font-semibold">{{ $payment->book->title ?? 'N/A' }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500">Amount</span>
            <p class="text-2xl font-bold text-gray-900">Rs. {{ number_format($payment->amount ?? 0, 2) }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500">Payment Gateway</span>
            <p class="font-semibold">{{ ucfirst($payment->gateway ?? 'N/A') }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500">Date</span>
            <p class="font-semibold">{{ $payment->created_at->format('M d, Y H:i') }}</p>
        </div>
        @if($payment->paid_at)
            <div>
                <span class="text-sm text-gray-500">Paid At</span>
                <p class="font-semibold">{{ $payment->paid_at->format('M d, Y H:i') }}</p>
            </div>
        @endif
    </div>

    <!-- Update Status -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-bold mb-4">Update Status</h3>
        <form action="{{ route('admin.payments.update-status', $payment->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                <select name="status" required class="px-4 py-2 border rounded-lg w-full sm:w-auto">
                    <option value="pending" {{ $payment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ $payment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ $payment->status == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="cancelled" {{ $payment->status == 'cancelled' ? 'selected' : '' }}>Cancelled / Refunded</option>
                </select>
                <button type="submit"
                        onclick="return confirm('Update payment status?')"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold w-full sm:w-auto">
                    Update Status
                </button>
            </div>
        </form>
    </div>
    </div>
</div>
@endsection

