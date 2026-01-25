@extends('layouts.student')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <div class="bg-white rounded-lg shadow p-4 lg:p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg lg:text-xl font-bold text-gray-900">Payment History</h2>
            <a href="{{ route('student.courses.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-semibold">
                Browse Courses
            </a>
        </div>

        @if($payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Course</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Transaction ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ ($payments->currentPage() - 1) * $payments->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-semibold text-gray-900">{{ $payment->book->title ?? 'Course' }}</div>
                                    @if($payment->paymentMethod)
                                        <div class="text-xs text-gray-500">{{ $payment->paymentMethod->name }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-xs font-mono text-gray-700">{{ $payment->transaction_id }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-semibold text-gray-900">Rs. {{ number_format($payment->amount, 2) }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                        @if($payment->status === 'completed') bg-green-100 text-green-800
                                        @elseif($payment->status === 'failed') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $payment->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('student.payments.invoice', $payment->id) }}"
                                       class="text-xs text-blue-600 hover:text-blue-700 font-semibold mr-2">
                                        View
                                    </a>
                                    <a href="{{ route('student.payments.invoice', ['paymentId' => $payment->id, 'download' => 1]) }}"
                                       class="text-xs text-gray-600 hover:text-gray-800 font-semibold">
                                        Download PDF
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        @else
            <div class="py-10 text-center">
                <div class="mx-auto h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <p class="text-sm text-gray-600">No payments recorded yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
