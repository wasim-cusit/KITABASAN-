@extends('layouts.student')

@section('title', 'Payment - ' . $course->title)
@section('page-title', 'Complete Payment')

@section('content')
<div class="container mx-auto px-0 lg:px-4">
    <div class="max-w-3xl mx-auto">
        <!-- Course Summary -->
        <div class="bg-white rounded-lg shadow p-4 lg:p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h2 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2">{{ $course->title }}</h2>
                    <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($course->description, 120) }}</p>
                </div>
                @if($course->hasValidThumbnail())
                    <img src="{{ $course->getThumbnailUrl() }}" alt="{{ $course->title }}" class="w-20 h-20 lg:w-24 lg:h-24 object-cover rounded-lg ml-4" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="hidden w-20 h-20 lg:w-24 lg:h-24 rounded-lg ml-4 flex items-center justify-center bg-gradient-to-br from-blue-400 to-indigo-600 text-white text-2xl font-bold">{{ $course->getTitleInitial() }}</div>
                @else
                    <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-lg ml-4 flex items-center justify-center bg-gradient-to-br from-blue-400 to-indigo-600 text-white text-2xl font-bold">{{ $course->getTitleInitial() }}</div>
                @endif
            </div>
        </div>

        <!-- Payment Form -->
        <div class="bg-white rounded-lg shadow p-4 lg:p-6">
            <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-6">Payment Details</h3>

            <form action="{{ route('student.payments.store') }}" method="POST" id="paymentForm">
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">

                <!-- Course Price -->
                <div class="border-b pb-4 mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-700">Course Price:</span>
                        <span class="text-xl font-bold text-gray-900">Rs. {{ number_format($course->price, 2) }}</span>
                    </div>
                    <div id="fee-details" class="text-sm text-gray-500 hidden">
                        <div class="flex justify-between items-center">
                            <span>Transaction Fee:</span>
                            <span id="fee-amount">Rs. 0.00</span>
                        </div>
                        <div class="flex justify-between items-center mt-1 font-semibold">
                            <span>Total Amount:</span>
                            <span id="total-amount" class="text-blue-600">Rs. {{ number_format($course->price, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Select Payment Method @if($paymentMethods->count() > 0)<span class="text-red-500">*</span>@endif
                    </label>

                    @if($paymentMethods->count() > 0)
                        <div class="space-y-3">
                            @foreach($paymentMethods as $method)
                                <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:border-blue-400 transition-colors payment-method-option {{ $loop->first ? 'border-blue-500' : 'border-gray-200' }}">
                                    <input type="radio" name="payment_method_id" value="{{ $method->id }}"
                                           class="mt-1 payment-method-radio"
                                           data-fee-percentage="{{ $method->transaction_fee_percentage ?? 0 }}"
                                           data-fee-fixed="{{ $method->transaction_fee_fixed ?? 0 }}"
                                           {{ $loop->first ? 'checked' : '' }}
                                           @if($paymentMethods->count() > 0) required @endif>
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $method->name }}</div>
                                                @if($method->description)
                                                    <p class="text-sm text-gray-600 mt-1">{{ $method->description }}</p>
                                                @endif
                                            </div>
                                            @if($method->icon)
                                                <img src="{{ route('storage.serve', ['path' => ltrim(str_replace('\\', '/', $method->icon), '/')]) }}" alt="{{ $method->name }}" class="w-12 h-12 object-contain">
                                            @endif
                                        </div>
                                        @if(($method->transaction_fee_percentage ?? 0) > 0 || ($method->transaction_fee_fixed ?? 0) > 0)
                                            <p class="text-xs text-gray-500 mt-2">
                                                Fee:
                                                @if(($method->transaction_fee_percentage ?? 0) > 0)
                                                    {{ number_format($method->transaction_fee_percentage, 2) }}%
                                                @endif
                                                @if(($method->transaction_fee_fixed ?? 0) > 0)
                                                    @if(($method->transaction_fee_percentage ?? 0) > 0) + @endif
                                                    Rs. {{ number_format($method->transaction_fee_fixed, 2) }}
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-yellow-800 text-sm">
                                <strong>No payment methods available.</strong> Please contact support or try again later.
                            </p>
                        </div>
                        <input type="hidden" name="payment_method_id" value="">
                    @endif

                    @error('payment_method_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Summary -->
                <div class="bg-gray-50 rounded-lg p-4 lg:p-6 mb-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Payment Summary</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Course:</span>
                            <span class="text-gray-900 font-medium">{{ $course->title }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Price:</span>
                            <span class="text-gray-900 font-medium">Rs. {{ number_format($course->price, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm hidden" id="fee-row">
                            <span class="text-gray-600">Transaction Fee:</span>
                            <span class="text-gray-900 font-medium" id="fee-display">Rs. 0.00</span>
                        </div>
                        <div class="border-t pt-2 mt-2">
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-900">Total:</span>
                                <span class="text-xl font-bold text-blue-600" id="final-total">Rs. {{ number_format($course->price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="mb-6">
                    <label class="flex items-start">
                        <input type="checkbox" name="terms" value="1" required
                               class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">
                            I agree to the <a href="#" class="text-blue-600 hover:underline">Terms and Conditions</a> and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                        </span>
                    </label>
                    @error('terms')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" id="pay-button"
                            class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="button-text">Proceed to Payment</span>
                        <span id="button-loading" class="hidden">Processing...</span>
                    </button>
                    <a href="{{ route('student.courses.show', $course->id) }}"
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold text-center transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Security Notice -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-semibold mb-1">Secure Payment</p>
                    <p>Your payment information is encrypted and secure. We do not store your payment details.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const coursePrice = {{ $course->price }};
    const paymentMethodRadios = document.querySelectorAll('.payment-method-radio');
    const feeDetails = document.getElementById('fee-details');
    const feeRow = document.getElementById('fee-row');
    const feeAmount = document.getElementById('fee-amount');
    const feeDisplay = document.getElementById('fee-display');
    const totalAmount = document.getElementById('total-amount');
    const finalTotal = document.getElementById('final-total');
    const paymentForm = document.getElementById('paymentForm');
    const payButton = document.getElementById('pay-button');
    const buttonText = document.getElementById('button-text');
    const buttonLoading = document.getElementById('button-loading');

    // Update fee and total when payment method changes
    paymentMethodRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updatePaymentSummary(this);
            // Update visual selection
            document.querySelectorAll('.payment-method-option').forEach(option => {
                option.classList.remove('border-blue-500');
                option.classList.add('border-gray-200');
            });
            this.closest('.payment-method-option').classList.remove('border-gray-200');
            this.closest('.payment-method-option').classList.add('border-blue-500');
        });
    });

    function updatePaymentSummary(radio) {
        if (!radio) return;

        const feePercentage = parseFloat(radio.dataset.feePercentage) || 0;
        const feeFixed = parseFloat(radio.dataset.feeFixed) || 0;

        const percentageFee = (coursePrice * feePercentage) / 100;
        const totalFee = percentageFee + feeFixed;
        const total = coursePrice + totalFee;

        if (totalFee > 0) {
            if (feeDetails) feeDetails.classList.remove('hidden');
            if (feeRow) feeRow.classList.remove('hidden');
            if (feeAmount) feeAmount.textContent = 'Rs. ' + totalFee.toFixed(2);
            if (feeDisplay) feeDisplay.textContent = 'Rs. ' + totalFee.toFixed(2);
            if (totalAmount) totalAmount.textContent = 'Rs. ' + total.toFixed(2);
            if (finalTotal) finalTotal.textContent = 'Rs. ' + total.toFixed(2);
        } else {
            if (feeDetails) feeDetails.classList.add('hidden');
            if (feeRow) feeRow.classList.add('hidden');
            if (totalAmount) totalAmount.textContent = 'Rs. ' + coursePrice.toFixed(2);
            if (finalTotal) finalTotal.textContent = 'Rs. ' + coursePrice.toFixed(2);
        }
    }

    // Initialize with first selected payment method
    const selectedRadio = document.querySelector('.payment-method-radio:checked');
    if (selectedRadio) {
        updatePaymentSummary(selectedRadio);
    }

    // Handle form submission
    paymentForm.addEventListener('submit', function(e) {
        payButton.disabled = true;
        buttonText.classList.add('hidden');
        buttonLoading.classList.remove('hidden');
    });
});
</script>
@endpush
@endsection
