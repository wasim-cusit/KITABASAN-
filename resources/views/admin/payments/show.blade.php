@extends('layouts.admin')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-3xl">
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div>
            <span class="text-sm text-gray-500">Transaction ID</span>
            <p class="font-mono font-semibold">{{ $payment->transaction_id }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500">Status</span>
            <p>
                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                    {{ $payment->status == 'completed' ? 'bg-green-100 text-green-800' : 
                       ($payment->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                       ($payment->status == 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </p>
        </div>
        <div>
            <span class="text-sm text-gray-500">User</span>
            <p class="font-semibold">{{ $payment->user->name }}</p>
            <p class="text-sm text-gray-600">{{ $payment->user->email }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500">Course</span>
            <p class="font-semibold">{{ $payment->book->title }}</p>
        </div>
        <div>
            <span class="text-sm text-gray-500">Amount</span>
            <p class="text-2xl font-bold text-green-600">Rs. {{ number_format($payment->amount, 2) }}</p>
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
            <div class="flex gap-4">
                <select name="status" required class="px-4 py-2 border rounded-lg">
                    <option value="pending" {{ $payment->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ $payment->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ $payment->status == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ $payment->status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

