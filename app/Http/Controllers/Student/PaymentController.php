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

        try {
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
                return redirect()->route('student.courses.enroll', $course->id)
                    ->with('info', 'This is a free course. You can enroll directly.');
            }

            // Validate course has a price
            if (!$course->price || $course->price <= 0) {
                return redirect()->route('student.courses.show', $course->id)
                    ->with('error', 'This course does not have a valid price. Please contact support.');
            }

            // Get active payment methods
            $paymentMethods = \App\Models\PaymentMethod::getActive();

            return view('student.payments.index', compact('course', 'paymentMethods'));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('student.courses.index')
                ->with('error', 'Course not found.');
        } catch (\Exception $e) {
            Log::error('Payment page error: ' . $e->getMessage());
            return redirect()->route('student.courses.index')
                ->with('error', 'An error occurred while loading the payment page. Please try again.');
        }
    }

    /**
     * Process payment
     */
    public function store(Request $request)
    {
        // Get active payment methods to determine if payment_method_id is required
        $activePaymentMethods = \App\Models\PaymentMethod::getActive();
        
        $validationRules = [
            'course_id' => 'required|exists:books,id',
            'terms' => 'required|accepted',
        ];

        // Make payment_method_id required if there are active payment methods
        if ($activePaymentMethods->count() > 0) {
            $validationRules['payment_method_id'] = 'required|exists:payment_methods,id';
        } else {
            $validationRules['payment_method_id'] = 'nullable|exists:payment_methods,id';
        }

        $request->validate($validationRules);

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

        // Validate course has a price
        if (!$course->price || $course->price <= 0) {
            return redirect()->back()
                ->with('error', 'This course does not have a valid price. Please contact support.');
        }

        try {
            DB::beginTransaction();

            // Get payment method if provided
            $paymentMethod = null;
            $gateway = 'manual';
            
            if ($request->payment_method_id) {
                $paymentMethod = \App\Models\PaymentMethod::find($request->payment_method_id);
                if ($paymentMethod) {
                    $gateway = $paymentMethod->code ?? 'manual';
                }
            }

            // Calculate total amount with fees
            $baseAmount = $course->price;
            $feeAmount = 0;
            
            if ($paymentMethod) {
                $feeAmount = $paymentMethod->calculateFee($baseAmount);
            }
            
            $totalAmount = $baseAmount + $feeAmount;

            // Generate unique transaction ID
            $transactionId = 'TXN' . strtoupper(\Illuminate\Support\Str::random(12)) . time();

            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'book_id' => $course->id,
                'transaction_id' => $transactionId,
                'gateway' => $gateway,
                'payment_method_id' => $request->payment_method_id,
                'amount' => $totalAmount, // Include fees in total amount
                'currency' => 'PKR',
                'status' => 'pending',
                'gateway_response' => null,
            ]);

            // Create transaction record
            Transaction::create([
                'payment_id' => $payment->id,
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $totalAmount,
                'description' => "Payment for course: {$course->title}" . ($feeAmount > 0 ? " (Fee: Rs. " . number_format($feeAmount, 2) . ")" : ""),
                'status' => 'pending',
                'notes' => "Transaction ID: {$transactionId}",
            ]);

            DB::commit();

            // Get gateway redirect data
            $gatewayData = $this->paymentService->getGatewayRedirectData($payment, $paymentMethod);

            // If gateway data is available and credentials are configured, redirect to gateway
            if ($gatewayData && $gatewayData['url']) {
                // Store payment ID in session for reference
                session(['payment_redirect_' . $transactionId => $payment->id]);
                
                // Return view with auto-submit form to redirect to payment gateway
                return view('student.payments.redirect', [
                    'gateway' => $gatewayData,
                    'payment' => $payment,
                    'course' => $course,
                ]);
            }

            // If no gateway configured or sandbox mode without credentials, show pending page
            // Admin can manually approve in sandbox mode
            return redirect()->route('student.payments.status', [
                'transaction_id' => $transactionId
            ])->with('info', $paymentMethod && $paymentMethod->is_sandbox 
                ? 'Your payment request has been submitted. It will be reviewed and activated once confirmed.'
                : 'Payment gateway is not properly configured. Please contact support.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while processing your payment: ' . $e->getMessage());
        }
    }

    /**
     * Show payment status page
     */
    public function status(Request $request)
    {
        $transactionId = $request->get('transaction_id');
        
        if (!$transactionId) {
            return redirect()->route('student.courses.index')
                ->with('error', 'Invalid payment transaction.');
        }

        $payment = Payment::where('transaction_id', $transactionId)
            ->where('user_id', Auth::id())
            ->with(['book', 'paymentMethod'])
            ->firstOrFail();

        $course = $payment->book;
        $enrollment = \App\Models\CourseEnrollment::where('user_id', Auth::id())
            ->where('book_id', $course->id)
            ->where('payment_status', 'paid')
            ->first();

        return view('student.payments.status', compact('payment', 'course', 'enrollment'));
    }

    /**
     * Handle payment callback/webhook from payment gateway
     */
    public function callback(Request $request)
    {
        // Handle different gateway response formats
        $transactionId = $request->input('transaction_id') 
            ?? $request->input('pp_BillReference') 
            ?? $request->input('orderRefNum')
            ?? $request->input('PP_TRAN_REF');
        
        // Determine status from gateway response
        $status = $this->determinePaymentStatus($request);

        if (!$transactionId) {
            Log::error('Payment callback: No transaction ID found', $request->all());
            return redirect()->route('student.courses.index')
                ->with('error', 'Invalid payment response. Please contact support if you have already made the payment.');
        }

        try {
            DB::beginTransaction();

            $payment = Payment::where('transaction_id', $transactionId)->first();

            if (!$payment) {
                DB::rollBack();
                Log::error("Payment callback: Payment not found for transaction ID: {$transactionId}");
                return redirect()->route('student.courses.index')
                    ->with('error', 'Payment record not found. Please contact support if you have already made the payment.');
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

    /**
     * Determine payment status from gateway response
     */
    private function determinePaymentStatus(Request $request): string
    {
        // Check if status is explicitly provided
        if ($request->has('status')) {
            $status = strtolower($request->input('status'));
            if (in_array($status, ['success', 'failed', 'cancelled', 'completed'])) {
                return $status === 'completed' ? 'success' : $status;
            }
        }

        // JazzCash response format
        if ($request->has('pp_ResponseCode')) {
            $responseCode = $request->input('pp_ResponseCode');
            // JazzCash: 000 = success, others = failed
            return ($responseCode === '000' || $responseCode === '0') ? 'success' : 'failed';
        }

        // EasyPaisa response format
        if ($request->has('responseCode')) {
            $responseCode = $request->input('responseCode');
            return ($responseCode === '0000' || $responseCode === '0') ? 'success' : 'failed';
        }

        // Check for success indicators
        if ($request->has('pp_ResponseMessage')) {
            $message = strtolower($request->input('pp_ResponseMessage'));
            if (strpos($message, 'success') !== false || strpos($message, 'approved') !== false) {
                return 'success';
            }
        }

        // Default to failed if no clear indication
        return 'failed';
    }
}