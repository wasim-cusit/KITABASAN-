<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\CourseEnrollment;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Str;

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

        // Verify payment with gateway
        // TODO: Implement payment verification

        if ($payment->status === 'completed') {
            return true;
        }

        // Activate course enrollment
        $book = Book::findOrFail($payment->book_id);
        $accessDuration = $book->access_duration_months;

        CourseEnrollment::updateOrCreate(
            [
                'user_id' => $payment->user_id,
                'book_id' => $payment->book_id,
            ],
            [
                'payment_id' => $payment->id,
                'status' => 'active',
                'enrolled_at' => now(),
                'expires_at' => now()->addMonths($accessDuration),
            ]
        );

        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

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

