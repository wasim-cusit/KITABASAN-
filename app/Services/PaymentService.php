<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\CourseEnrollment;
use App\Models\Book;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Process JazzCash payment
     */
    public function processJazzCashPayment(array $data): Payment
    {
        $payment = Payment::create([
            'user_id' => $data['user_id'],
            'book_id' => $data['book_id'],
            'transaction_id' => $this->generateTransactionId(),
            'gateway' => 'jazzcash',
            'amount' => $data['amount'],
            'status' => 'pending',
            'gateway_response' => $data['gateway_response'] ?? null,
        ]);

        // Process payment with JazzCash API
        // TODO: Implement JazzCash API integration

        return $payment;
    }

    /**
     * Process EasyPaisa payment
     */
    public function processEasyPaisaPayment(array $data): Payment
    {
        $payment = Payment::create([
            'user_id' => $data['user_id'],
            'book_id' => $data['book_id'],
            'transaction_id' => $this->generateTransactionId(),
            'gateway' => 'easypaisa',
            'amount' => $data['amount'],
            'status' => 'pending',
            'gateway_response' => $data['gateway_response'] ?? null,
        ]);

        // Process payment with EasyPaisa API
        // TODO: Implement EasyPaisa API integration

        return $payment;
    }

    /**
     * Verify payment and activate course
     */
    public function verifyAndActivatePayment(string $transactionId): bool
    {
        $payment = Payment::where('transaction_id', $transactionId)->firstOrFail();

        // If payment is already completed, check if enrollment exists and is active
        if ($payment->status === 'completed') {
            $enrollment = CourseEnrollment::where('payment_id', $payment->id)
                ->where('user_id', $payment->user_id)
                ->where('book_id', $payment->book_id)
                ->first();

            // If enrollment doesn't exist, create it (idempotency check failed scenario)
            if (!$enrollment) {
                return $this->activateEnrollment($payment);
            }

            return true;
        }

        // Verify payment with gateway
        // TODO: Implement payment verification with actual gateway API

        // If payment status is pending or failed, don't activate
        if ($payment->status === 'failed' || $payment->status === 'cancelled') {
            return false;
        }

        // Mark payment as completed and activate enrollment
        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        return $this->activateEnrollment($payment);
    }

    /**
     * Activate course enrollment after successful payment
     */
    public function activateEnrollment(Payment $payment): bool
    {
        // Prevent duplicate enrollments (idempotency check)
        $existingEnrollment = CourseEnrollment::where('user_id', $payment->user_id)
            ->where('book_id', $payment->book_id)
            ->where('payment_status', 'paid')
            ->where('status', 'active')
            ->first();

        if ($existingEnrollment && $existingEnrollment->payment_id === $payment->id) {
            // Enrollment already exists and is linked to this payment
            return true;
        }

        // Get course details
        $book = Book::findOrFail($payment->book_id);
        $accessDuration = $book->access_duration_months ?? $book->duration_months ?? 12; // Default to 12 months

        // Create or update enrollment with payment link
        CourseEnrollment::updateOrCreate(
            [
                'user_id' => $payment->user_id,
                'book_id' => $payment->book_id,
            ],
            [
                'payment_id' => $payment->id,
                'status' => 'active',
                'payment_status' => 'paid',
                'enrolled_at' => now(),
                'expires_at' => $accessDuration ? now()->addMonths($accessDuration) : null,
            ]
        );

        return true;
    }

    /**
     * Handle payment success callback/webhook
     */
    public function handlePaymentSuccess(string $transactionId, array $gatewayData = []): bool
    {
        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            Log::error("Payment not found for transaction ID: {$transactionId}");
            return false;
        }

        // Prevent duplicate processing (idempotency)
        if ($payment->status === 'completed') {
            // Payment already processed, ensure enrollment is active
            $this->activateEnrollment($payment);
            return true;
        }

        // Update payment with gateway response
        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
            'gateway_response' => $gatewayData,
        ]);

        // Automatically activate enrollment
        return $this->activateEnrollment($payment);
    }

    /**
     * Handle payment failure callback/webhook
     */
    public function handlePaymentFailure(string $transactionId, array $gatewayData = []): bool
    {
        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            Log::error("Payment not found for transaction ID: {$transactionId}");
            return false;
        }

        // Update payment status
        $payment->update([
            'status' => 'failed',
            'gateway_response' => $gatewayData,
        ]);

        // Do NOT activate enrollment on failure
        return false;
    }

    /**
     * Handle payment refund (using 'cancelled' status)
     */
    public function handleRefund(string $transactionId, array $gatewayData = []): bool
    {
        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            Log::error("Payment not found for transaction ID: {$transactionId}");
            return false;
        }

        // Update payment status to cancelled (refunded)
        $payment->update([
            'status' => 'cancelled',
            'gateway_response' => is_array($payment->gateway_response)
                ? array_merge($payment->gateway_response, $gatewayData)
                : $gatewayData,
        ]);

        // Revoke enrollment access
        $enrollment = CourseEnrollment::where('payment_id', $payment->id)->first();
        if ($enrollment) {
            $enrollment->update([
                'status' => 'cancelled',
            ]);
        }

        return true;
    }

    /**
     * Generate unique transaction ID
     */
    private function generateTransactionId(): string
    {
        return 'TXN' . strtoupper(Str::random(12)) . time();
    }
}

