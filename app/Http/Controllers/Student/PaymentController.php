<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display payment page for a course
     */
    public function index(Request $request)
    {
        $courseId = $request->get('course_id');
        
        if (!$courseId) {
            return redirect()->route('student.courses.index')
                ->with('error', 'Please select a course to purchase.');
        }

        $course = Book::findOrFail($courseId);
        $user = Auth::user();

        // Check if already enrolled
        $enrollment = \App\Models\CourseEnrollment::where('user_id', $user->id)
            ->where('book_id', $course->id)
            ->where('payment_status', 'paid')
            ->first();

        if ($enrollment) {
            return redirect()->route('student.learning.index', $course->id)
                ->with('info', 'You are already enrolled in this course.');
        }

        // Check if free course
        if ($course->is_free) {
            return redirect()->route('student.courses.enroll', $course->id);
        }

        // Get active payment methods
        $paymentMethods = \App\Models\PaymentMethod::getActive();

        return view('student.payments.index', compact('course', 'paymentMethods'));
    }

    /**
     * Process payment
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:books,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
        ]);

        $user = Auth::user();
        $course = Book::findOrFail($request->course_id);

        // Check if already enrolled
        $existingEnrollment = \App\Models\CourseEnrollment::where('user_id', $user->id)
            ->where('book_id', $course->id)
            ->where('payment_status', 'paid')
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('student.learning.index', $course->id)
                ->with('info', 'You are already enrolled in this course.');
        }

        // Check if free course
        if ($course->is_free) {
            return redirect()->route('student.courses.enroll', $course->id);
        }

        try {
            DB::beginTransaction();

            // Generate unique transaction ID
            $transactionId = 'TXN' . strtoupper(\Illuminate\Support\Str::random(12)) . time();

            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'book_id' => $course->id,
                'transaction_id' => $transactionId,
                'gateway' => 'manual', // Will be updated based on payment gateway
                'payment_method_id' => $request->payment_method_id,
                'amount' => $course->price ?? 0,
                'status' => 'pending',
                'gateway_response' => null,
            ]);

            // Create transaction record
            Transaction::create([
                'payment_id' => $payment->id,
                'user_id' => $user->id,
                'type' => 'debit', // Debit for payment (money going out from user's perspective)
                'amount' => $course->price ?? 0,
                'description' => "Payment for course: {$course->title}",
                'status' => 'pending',
                'notes' => "Transaction ID: {$transactionId}",
            ]);

            DB::commit();

            // TODO: Redirect to payment gateway or process payment
            // For now, redirect to callback for testing (in production, this would redirect to gateway)
            return redirect()->route('student.payments.callback', [
                'transaction_id' => $transactionId,
                'status' => 'success' // In production, this comes from gateway
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'An error occurred while processing your payment. Please try again.');
        }
    }

    /**
     * Handle payment callback/webhook from payment gateway
     */
    public function callback(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'status' => 'required|in:success,failed,cancelled',
        ]);

        $transactionId = $request->transaction_id;
        $status = $request->status;

        try {
            DB::beginTransaction();

            $payment = Payment::where('transaction_id', $transactionId)->first();

            if (!$payment) {
                DB::rollBack();
                Log::error("Payment callback: Payment not found for transaction ID: {$transactionId}");
                return redirect()->route('student.payments.index', ['course_id' => $request->course_id ?? null])
                    ->with('error', 'Payment record not found.');
            }

            // Check if payment is already processed (idempotency)
            if ($payment->status === 'completed' && $status === 'success') {
                DB::commit();
                // Already processed, redirect to course
                return redirect()->route('student.learning.index', $payment->book_id)
                    ->with('success', 'Payment already processed. Welcome to your course!');
            }

            // Update transaction record
            $transaction = Transaction::where('payment_id', $payment->id)->first();
            if ($transaction) {
                $transaction->update([
                    'status' => $status === 'success' ? 'completed' : 'failed',
                    'notes' => ($transaction->notes ?? '') . "\nCallback received: " . now()->toDateTimeString(),
                ]);
            }

            if ($status === 'success') {
                // Handle successful payment
                $gatewayData = [
                    'callback_received_at' => now()->toDateTimeString(),
                    'callback_data' => $request->except(['transaction_id', 'status']),
                ];

                $this->paymentService->handlePaymentSuccess($transactionId, $gatewayData);

                DB::commit();

                return redirect()->route('student.learning.index', $payment->book_id)
                    ->with('success', 'Payment successful! Your course is now active.');
            } else {
                // Handle failed/cancelled payment
                $gatewayData = [
                    'callback_received_at' => now()->toDateTimeString(),
                    'status' => $status,
                    'callback_data' => $request->except(['transaction_id', 'status']),
                ];

                $this->paymentService->handlePaymentFailure($transactionId, $gatewayData);

                DB::commit();

                return redirect()->route('student.courses.show', $payment->book_id)
                    ->with('error', 'Payment ' . $status . '. Please try again.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment callback error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'status' => $status,
                'error' => $e->getTraceAsString(),
            ]);

            return redirect()->route('student.courses.index')
                ->with('error', 'An error occurred while processing payment callback. Please contact support.');
        }
    }

    /**
     * Handle payment webhook (for server-to-server communication)
     */
    public function webhook(Request $request)
    {
        // TODO: Implement webhook signature verification for security
        // TODO: Implement gateway-specific webhook handling

        $transactionId = $request->input('transaction_id') ?? $request->input('txn_id');
        $status = $request->input('status') ?? $request->input('payment_status');

        if (!$transactionId || !$status) {
            Log::error('Payment webhook: Missing required fields', $request->all());
            return response()->json(['error' => 'Missing required fields'], 400);
        }

        try {
            DB::beginTransaction();

            $payment = Payment::where('transaction_id', $transactionId)->first();

            if (!$payment) {
                DB::rollBack();
                Log::error("Payment webhook: Payment not found for transaction ID: {$transactionId}");
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Prevent duplicate processing (idempotency)
            if ($payment->status === 'completed' && $status === 'success') {
                DB::commit();
                return response()->json(['message' => 'Payment already processed'], 200);
            }

            // Update transaction record
            $transaction = Transaction::where('payment_id', $payment->id)->first();
            if ($transaction) {
                $transaction->update([
                    'status' => $status === 'success' ? 'completed' : 'failed',
                    'notes' => ($transaction->notes ?? '') . "\nWebhook received: " . now()->toDateTimeString(),
                ]);
            }

            $gatewayData = [
                'webhook_received_at' => now()->toDateTimeString(),
                'status' => $status,
                'webhook_data' => $request->all(),
            ];

            if ($status === 'success' || $status === 'completed') {
                $this->paymentService->handlePaymentSuccess($transactionId, $gatewayData);
            } elseif ($status === 'cancelled' || $status === 'refunded') {
                $this->paymentService->handleRefund($transactionId, $gatewayData);
            } elseif ($status === 'failed') {
                $this->paymentService->handlePaymentFailure($transactionId, $gatewayData);
            }

            DB::commit();

            return response()->json(['message' => 'Webhook processed successfully'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment webhook error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'status' => $status,
                'error' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }
}