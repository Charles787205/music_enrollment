<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display payment collection dashboard for employees.
     */
    public function index()
    {
        // Only employees and admins can access payment collection
        if (!Auth::user()->isEmployee() && !Auth::user()->isAdmin()) {
            abort(403, 'Access denied. Employee privileges required.');
        }

        $enrollments = CourseEnrollment::with(['user', 'course', 'teacher', 'collector'])
            ->whereIn('payment_status', ['pending', 'partial'])
            ->orderBy('payment_due_date', 'asc')
            ->paginate(15);

        $stats = [
            'pending_payments' => CourseEnrollment::where('payment_status', 'pending')->count(),
            'partial_payments' => CourseEnrollment::where('payment_status', 'partial')->count(),
            'overdue_payments' => CourseEnrollment::where('payment_due_date', '<', now())
                ->whereIn('payment_status', ['pending', 'partial'])
                ->count(),
            'total_outstanding' => CourseEnrollment::whereIn('payment_status', ['pending', 'partial'])
                ->get()
                ->sum(function ($enrollment) {
                    return $enrollment->getRemainingBalance();
                })
        ];

        return view('payments.index', compact('enrollments', 'stats'));
    }

    /**
     * Show payment collection form for specific enrollment.
     */
    public function show(CourseEnrollment $enrollment)
    {
        if (!Auth::user()->isEmployee() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $enrollment->load(['user', 'course', 'teacher', 'collector']);

        return view('payments.show', compact('enrollment'));
    }

    /**
     * Process payment collection.
     */
    public function collect(Request $request, CourseEnrollment $enrollment)
    {
        if (!Auth::user()->isEmployee() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $enrollment->getRemainingBalance(),
            'payment_method' => 'required|in:cash,card,bank_transfer,check',
            'notes' => 'nullable|string|max:500',
        ]);

        $amount = $request->amount;
        $newAmountPaid = $enrollment->amount_paid + $amount;
        
        // Determine new payment status
        $newStatus = 'partial';
        if ($newAmountPaid >= $enrollment->total_fee) {
            $newStatus = 'paid';
        }

        $enrollment->update([
            'amount_paid' => $newAmountPaid,
            'payment_status' => $newStatus,
            'collected_by' => Auth::id(),
            'payment_collected_at' => now(),
            'payment_notes' => $request->notes
        ]);

        // Auto-enroll student when payment is fully paid
        if ($newStatus === 'paid' && $enrollment->user->isStudent()) {
            $enrollment->user->update([
                'is_enrolled' => true
            ]);
            
            $successMessage = 'Payment of $' . number_format($amount, 2) . ' collected successfully! Student has been automatically enrolled.';
        } else {
            $successMessage = 'Payment of $' . number_format($amount, 2) . ' collected successfully!';
        }

        return redirect()->route('employee.payments.show', $enrollment)
            ->with('success', $successMessage);
    }

    /**
     * Show payment history for an enrollment.
     */
    public function history(CourseEnrollment $enrollment)
    {
        if (!Auth::user()->isEmployee() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // For now, we'll show basic info. Later you can create a payments table for detailed history
        return view('payments.history', compact('enrollment'));
    }

    /**
     * Generate payment reports.
     */
    public function reports()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Admin access required');
        }

        $monthlyCollections = CourseEnrollment::selectRaw('
                YEAR(payment_collected_at) as year,
                MONTH(payment_collected_at) as month,
                SUM(amount_paid) as total_collected,
                COUNT(*) as payment_count
            ')
            ->whereNotNull('payment_collected_at')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        $employeeCollections = User::select('users.*')
            ->withCount('collectedPayments')
            ->withSum('collectedPayments', 'amount_paid')
            ->where('user_type', 'employee')
            ->orderBy('collected_payments_sum_amount_paid', 'desc')
            ->get();

        return view('payments.reports', compact('monthlyCollections', 'employeeCollections'));
    }
}