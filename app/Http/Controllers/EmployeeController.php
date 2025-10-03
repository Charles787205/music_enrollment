<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Instrument;
use App\Models\InstrumentBorrow;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Show the employee dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_students' => User::where('user_type', 'student')->count(),
            'enrolled_students' => User::where('user_type', 'student')
                ->where('is_enrolled', true)->count(),
            'total_instruments' => Instrument::count(),
            'available_instruments' => Instrument::where('is_available', true)->count(),
            'total_borrows' => InstrumentBorrow::count(),
            'active_borrows' => InstrumentBorrow::where('status', 'borrowed')->count(),
            'pending_borrows' => InstrumentBorrow::where('status', 'pending')->count(),
            'overdue_borrows' => InstrumentBorrow::where('status', 'overdue')->count(),
        ];

        $recent_borrows = InstrumentBorrow::with(['user', 'instrument'])
            ->latest()
            ->take(10)
            ->get();

        $pending_borrows = InstrumentBorrow::with(['user', 'instrument'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('employee.dashboard', compact('stats', 'recent_borrows', 'pending_borrows'));
    }

    /**
     * View students.
     */
    public function students(Request $request)
    {
        $query = User::where('user_type', 'student');

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('enrollment_status')) {
            if ($request->enrollment_status === 'enrolled') {
                $query->where('is_enrolled', true);
            } elseif ($request->enrollment_status === 'not_enrolled') {
                $query->where('is_enrolled', false);
            }
        }

        $students = $query->latest()->paginate(15);

        return view('employee.students.index', compact('students'));
    }

    /**
     * Show student details.
     */
    public function showStudent(User $student)
    {
        if (!$student->isStudent()) {
            abort(404);
        }

        $enrollments = $student->enrollments()->with('instrument')->latest()->get();
        return view('employee.students.show', compact('student', 'enrollments'));
    }

    /**
     * Manage instrument borrows (formerly enrollments).
     */
    public function enrollments(Request $request)
    {
        $query = InstrumentBorrow::with(['user', 'instrument']);

        if ($request->has('search') && $request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhereHas('instrument', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->latest()->paginate(15);

        return view('employee.enrollments.index', compact('enrollments'));
    }

    /**
     * Update instrument borrow status.
     */
    public function updateEnrollmentStatus(Request $request, InstrumentBorrow $enrollment)
    {
        $request->validate([
            'status' => ['required', 'in:pending,borrowed,returned,overdue'],
        ]);

        $oldStatus = $enrollment->status;
        $newStatus = $request->status;

        // Update the status
        $updateData = ['status' => $newStatus];

        // Set appropriate timestamps based on status transitions
        if ($newStatus === 'borrowed' && $oldStatus === 'pending') {
            $updateData['borrowed_at'] = now();
            $updateData['due_date'] = now()->addWeeks(2); // Default 2-week borrowing period
        } elseif ($newStatus === 'returned' && in_array($oldStatus, ['borrowed', 'overdue'])) {
            $updateData['returned_at'] = now();
        } elseif ($newStatus === 'overdue' && $oldStatus === 'borrowed') {
            // Keep the original borrowed_at and due_date
        }

        $enrollment->update($updateData);

        // Create a success message based on the status change
        $statusMessages = [
            'borrowed' => 'Instrument borrow approved successfully. Due date set to ' . 
                         $enrollment->fresh()->due_date?->format('M j, Y'),
            'returned' => 'Instrument marked as returned successfully.',
            'overdue' => 'Instrument marked as overdue.',
            'pending' => 'Status updated to pending.',
        ];

        $message = $statusMessages[$newStatus] ?? 'Borrow status updated successfully.';

        return back()->with('success', $message);
    }

    /**
     * Manage course enrollments.
     */
    public function courseEnrollments(Request $request)
    {
        $query = CourseEnrollment::with(['student', 'course.teacher']);

        if ($request->has('search') && $request->search) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhereHas('course', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status') && $request->payment_status) {
            if ($request->payment_status === 'overdue') {
                $query->where('payment_status', 'pending')
                      ->where('payment_due_date', '<', now());
            } else {
                $query->where('payment_status', $request->payment_status);
            }
        }

        $courseEnrollments = $query->latest()->paginate(15);

        return view('employee.course-enrollments.index', compact('courseEnrollments'));
    }

    /**
     * Update course enrollment status.
     */
    public function updateCourseEnrollmentStatus(Request $request, CourseEnrollment $courseEnrollment)
    {
        $request->validate([
            'status' => ['required', 'in:pending,enrolled,completed,dropped'],
        ]);

        $oldStatus = $courseEnrollment->status;
        $newStatus = $request->status;

        // Update the status
        $updateData = ['status' => $newStatus];

        // Set appropriate timestamps based on status transitions
        if ($newStatus === 'enrolled' && $oldStatus === 'pending') {
            $updateData['enrolled_at'] = now();
        } elseif ($newStatus === 'completed' && $oldStatus === 'enrolled') {
            $updateData['completed_at'] = now();
        }

        $courseEnrollment->update($updateData);

        // Create a success message based on the status change
        $statusMessages = [
            'approved' => 'Course enrollment approved successfully.',
            'rejected' => 'Course enrollment rejected.',
            'completed' => 'Course enrollment marked as completed.',
            'pending' => 'Status updated to pending.',
        ];

        $message = $statusMessages[$newStatus] ?? 'Course enrollment status updated successfully.';

        return back()->with('success', $message);
    }
}