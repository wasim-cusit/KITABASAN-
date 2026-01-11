<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'book']);

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('transaction_id', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                                ->orWhere('email', 'like', '%' . $request->search . '%');
                  })
                  ->orWhereHas('book', function($bookQuery) use ($request) {
                      $bookQuery->where('title', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment gateway
        if ($request->has('gateway') && $request->gateway) {
            $query->where('gateway', $request->gateway);
        }

        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(20);

        // Statistics
        $stats = [
            'total' => Payment::count(),
            'completed' => Payment::where('status', 'completed')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount') ?? 0,
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    public function show($id)
    {
        $payment = Payment::with(['user', 'book'])->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }

    public function updateStatus(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,completed,failed,cancelled',
        ]);

        $oldStatus = $payment->status;
        $newStatus = $request->status;

        $payment->update([
            'status' => $newStatus,
            'paid_at' => $newStatus === 'completed' ? now() : null,
        ]);

        // Automatically activate enrollment when payment is marked as completed
        if ($newStatus === 'completed' && $oldStatus !== 'completed') {
            $paymentService = app(\App\Services\PaymentService::class);
            try {
                $paymentService->activateEnrollment($payment);
            } catch (\Exception $e) {
                \Log::error('Failed to activate enrollment when updating payment status: ' . $e->getMessage());
                return redirect()->back()
                    ->with('warning', 'Payment status updated, but enrollment activation failed. Please check logs.');
            }
        }

        // If payment is cancelled (refunded), revoke enrollment
        if ($newStatus === 'cancelled' && $oldStatus === 'completed') {
            $enrollment = \App\Models\CourseEnrollment::where('payment_id', $payment->id)->first();
            if ($enrollment) {
                $enrollment->update([
                    'status' => 'cancelled',
                ]);
            }
        }

        return redirect()->back()
            ->with('success', 'Payment status updated successfully.');
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment record deleted successfully.');
    }
}
