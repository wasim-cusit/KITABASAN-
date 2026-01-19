@extends('layouts.student')

@section('title', 'Payment Status - ' . $course->title)
@section('page-title', 'Payment Status')

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <div class="max-w-3xl mx-auto">
        <!-- Payment Status Card -->
        <div class="bg-white rounded-lg shadow p-4 lg:p-6 mb-6">
            <div class="text-center mb-6">
                @if($payment->status === 'completed')
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-green-600 mb-2">Payment Successful!</h2>
                    <p class="text-gray-600">Your payment has been confirmed and your course is now active.</p>
                @elseif($payment->status === 'failed')
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-red-600 mb-2">Payment Failed</h2>
                    <p class="text-gray-600">Your payment could not be processed. Please try again.</p>
                @else
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                        <svg class="h-8 w-8 text-yellow-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-yellow-600 mb-2">Payment Pending</h2>
                    <p class="text-gray-600">Your payment is being processed. Please wait for confirmation.</p>
                @endif
            </div>

            <!-- Payment Details -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Details</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Course:</span>
                        <span class="font-medium text-gray-900">{{ $course->title }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Transaction ID:</span>
                        <span class="font-mono text-sm text-gray-900">{{ $payment->transaction_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount:</span>
                        <span class="font-medium text-gray-900">Rs. {{ number_format($payment->amount, 2) }}</span>
                    </div>
                    @if($payment->paymentMethod)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Method:</span>
                            <span class="font-medium text-gray-900">{{ $payment->paymentMethod->name }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($payment->status === 'completed') bg-green-100 text-green-800
                            @elseif($payment->status === 'failed') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date:</span>
                        <span class="font-medium text-gray-900">{{ $payment->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
            @if($payment->status === 'completed' && $enrollment)
                <a href="{{ route('student.learning.index', $course->id) }}"
                   class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold text-center transition-colors">
                    Start Learning
                </a>
            @elseif($payment->status === 'pending')
                <a href="{{ route('student.payments.status', ['transaction_id' => $payment->transaction_id]) }}"
                   class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold text-center transition-colors">
                    Refresh Status
                </a>
                <a href="{{ route('student.courses.show', $course->id) }}"
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold text-center transition-colors">
                    Back to Course
                </a>
            @elseif($payment->status === 'failed')
                <a href="{{ route('student.payments.index', ['course_id' => $course->id]) }}"
                   class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold text-center transition-colors">
                    Try Again
                </a>
                <a href="{{ route('student.courses.show', $course->id) }}"
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold text-center transition-colors">
                    Back to Course
                </a>
            @else
                <a href="{{ route('student.courses.show', $course->id) }}"
                   class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold text-center transition-colors">
                    Back to Course
                </a>
            @endif
        </div>

        <!-- Information Notice -->
        @if($payment->status === 'pending')
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Payment Processing</p>
                        <p>Your payment request has been submitted and is being processed. Once your payment is confirmed, you will be able to access the course. This may take a few minutes. You can refresh this page to check the status.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
