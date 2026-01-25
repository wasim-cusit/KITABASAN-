@extends('layouts.student')

@section('title', 'Invoice - ' . ($payment->transaction_id ?? 'Payment'))
@section('page-title', 'Invoice')

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-xl lg:text-2xl font-bold text-gray-900">Payment Invoice</h2>
                    <p class="text-sm text-gray-600">Invoice ID: {{ $payment->transaction_id }}</p>
                    <p class="text-sm text-gray-600">Date: {{ $payment->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500">Status</div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                        @if($payment->status === 'completed') bg-green-100 text-green-800
                        @elseif($payment->status === 'failed') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Billed To</h3>
                    <p class="text-sm text-gray-900">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Payment Method</h3>
                    <p class="text-sm text-gray-900">{{ $payment->paymentMethod->name ?? 'Manual' }}</p>
                    <p class="text-xs text-gray-500">Currency: {{ $payment->currency ?? 'PKR' }}</p>
                </div>
            </div>

            <div class="border rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <tr>
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-gray-900">{{ $course->title ?? 'Course' }}</div>
                                <div class="text-xs text-gray-500">Course purchase</div>
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-gray-900">
                                Rs. {{ number_format($course->price ?? $payment->amount, 2) }}
                            </td>
                        </tr>
                        @if($feeAmount > 0)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-600">Transaction Fee</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-900">Rs. {{ number_format($feeAmount, 2) }}</td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Total Paid</td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">Rs. {{ number_format($payment->amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('student.payments.invoice', ['paymentId' => $payment->id, 'download' => 1]) }}"
                   class="flex-1 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-semibold text-center">
                    Download PDF
                </a>
                <a href="{{ route('student.payments.index') }}"
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold text-center">
                    Back to Payments
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
