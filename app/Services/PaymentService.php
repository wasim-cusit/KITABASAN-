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
        // Null means lifetime access, use access_duration_months or fallback to duration_months
        $accessDuration = $book->access_duration_months ?? $book->duration_months;

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
                'expires_at' => $accessDuration ? now()->addMonths($accessDuration) : null, // null = lifetime access
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

    /**
     * Get payment gateway redirect URL and form data
     */
    public function getGatewayRedirectData(Payment $payment, $paymentMethod)
    {
        if (!$paymentMethod) {
            return null;
        }

        $gatewayCode = $paymentMethod->code;
        $credentials = $paymentMethod->credentials ?? [];
        $config = $paymentMethod->config ?? [];
        $isSandbox = $paymentMethod->is_sandbox ?? true;

        // Get base URL (sandbox or production)
        $baseUrl = $isSandbox 
            ? ($config['sandbox_url'] ?? 'https://sandbox.jazzcash.com.pk')
            : ($config['production_url'] ?? 'https://payments.jazzcash.com.pk');

        // Get callback URLs
        $callbackUrl = route('student.payments.callback', [
            'transaction_id' => $payment->transaction_id
        ], true);

        switch ($gatewayCode) {
            case 'jazzcash':
                return $this->prepareJazzCashRedirect($payment, $credentials, $baseUrl, $callbackUrl, $isSandbox);
            
            case 'easypaisa':
                return $this->prepareEasyPaisaRedirect($payment, $credentials, $baseUrl, $callbackUrl, $isSandbox);
            
            default:
                Log::warning("Unknown payment gateway: {$gatewayCode}");
                return null;
        }
    }

    /**
     * Prepare JazzCash payment redirect data
     */
    private function prepareJazzCashRedirect($payment, $credentials, $baseUrl, $callbackUrl, $isSandbox)
    {
        $merchantId = $credentials['merchant_id'] ?? '';
        $password = $credentials['password'] ?? '';
        $integritySalt = $credentials['integrity_salt'] ?? '';

        if (empty($merchantId) || empty($password) || empty($integritySalt)) {
            Log::error('JazzCash credentials not configured');
            return null;
        }

        // JazzCash requires specific format
        $ppAmount = number_format($payment->amount, 2, '.', '');
        $ppBillReference = $payment->transaction_id;
        $ppDescription = "Course Payment - Transaction: {$payment->transaction_id}";
        $ppReturnUrl = $callbackUrl;
        
        // Generate integrity hash (JazzCash specific format)
        // Format: pp_Amount&pp_BillReference&pp_Description&pp_MerchantID&pp_Password&pp_ReturnURL
        $hashString = $ppAmount . '&' . $ppBillReference . '&' . $ppDescription . '&' . 
                     $merchantId . '&' . $password . '&' . $ppReturnUrl;
        $ppSecureHash = hash_hmac('sha256', $hashString, $integritySalt);

        // JazzCash payment URL
        $paymentUrl = $isSandbox 
            ? 'https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/'
            : 'https://payments.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/';

        return [
            'url' => $paymentUrl,
            'method' => 'POST',
            'fields' => [
                'pp_Version' => '1.1',
                'pp_TxnType' => 'MWALLET',
                'pp_Language' => 'EN',
                'pp_MerchantID' => $merchantId,
                'pp_SubMerchantID' => '',
                'pp_Password' => $password,
                'pp_BankID' => '',
                'pp_ProductID' => '',
                'pp_TxnRefNo' => $ppBillReference,
                'pp_Amount' => $ppAmount,
                'pp_TxnCurrency' => 'PKR',
                'pp_TxnDateTime' => date('YmdHis'),
                'pp_BillReference' => $ppBillReference,
                'pp_Description' => $ppDescription,
                'pp_TxnExpiryDateTime' => date('YmdHis', strtotime('+1 hour')),
                'pp_ReturnURL' => $ppReturnUrl,
                'pp_SecureHash' => $ppSecureHash,
                'ppmpf_1' => '',
                'ppmpf_2' => '',
                'ppmpf_3' => '',
                'ppmpf_4' => '',
                'ppmpf_5' => '',
            ]
        ];
    }

    /**
     * Prepare EasyPaisa payment redirect data
     */
    private function prepareEasyPaisaRedirect($payment, $credentials, $baseUrl, $callbackUrl, $isSandbox)
    {
        $merchantId = $credentials['merchant_id'] ?? '';
        $password = $credentials['password'] ?? '';
        $storeId = $credentials['store_id'] ?? '';

        if (empty($merchantId) || empty($password)) {
            Log::error('EasyPaisa credentials not configured');
            return null;
        }

        // EasyPaisa payment URL
        $paymentUrl = $isSandbox 
            ? 'https://easypay.easypaisa.com.pk/easypay/Index.jsf'
            : 'https://easypay.easypaisa.com.pk/easypay/Index.jsf';

        $amount = number_format($payment->amount, 2, '.', '');
        $orderRefNum = $payment->transaction_id;
        $postBackURL = $callbackUrl;

        return [
            'url' => $paymentUrl,
            'method' => 'POST',
            'fields' => [
                'storeId' => $storeId ?: $merchantId,
                'merchantId' => $merchantId,
                'password' => $password,
                'orderRefNum' => $orderRefNum,
                'paymentMethod' => 'OTC',
                'amount' => $amount,
                'postBackURL' => $postBackURL,
                'expiryDate' => date('YmdHis', strtotime('+1 hour')),
            ]
        ];
    }
}

