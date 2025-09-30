<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Instrument;
use App\Models\InstrumentBorrow;
use App\Models\CourseEnrollment;
use App\Models\Course;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('user_type', 'student')->count(),
            'total_employees' => User::where('user_type', 'employee')->count(),
            'total_courses' => Course::count(),
            'active_courses' => Course::where('status', 'active')->count(),
            'total_instruments' => Instrument::count(),
            'available_instruments' => Instrument::where('is_available', true)->count(),
            'total_course_enrollments' => CourseEnrollment::count(),
            'active_course_enrollments' => CourseEnrollment::where('status', 'active')->count(),
            'pending_course_enrollments' => CourseEnrollment::where('status', 'pending')->count(),
            'total_instrument_borrows' => InstrumentBorrow::count(),
            'active_instrument_borrows' => InstrumentBorrow::where('status', 'borrowed')->count(),
            'pending_instrument_borrows' => InstrumentBorrow::where('status', 'pending')->count(),
        ];

        $recent_course_enrollments = CourseEnrollment::with(['user', 'course'])
            ->latest()
            ->take(5)
            ->get();

        $recent_instrument_borrows = InstrumentBorrow::with(['user', 'instrument'])
            ->latest()
            ->take(5)
            ->get();

        $recent_users = User::latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_course_enrollments', 'recent_instrument_borrows', 'recent_users'));
    }

    /**
     * Manage users.
     */
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('user_type') && $request->user_type) {
            $query->where('user_type', $request->user_type);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show user details.
     */
    public function showUser(User $user)
    {
        $courseEnrollments = $user->courseEnrollments()->with('course')->latest()->get();
        $instrumentBorrows = InstrumentBorrow::where('user_id', $user->id)->with('instrument')->latest()->get();
        return view('admin.users.show', compact('user', 'courseEnrollments', 'instrumentBorrows'));
    }

    /**
     * Edit user.
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user.
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'user_type' => ['required', 'in:student,employee,admin'],
            'is_enrolled' => ['boolean'],
        ]);

        $user->update($request->only([
            'name', 'email', 'phone', 'user_type', 'is_enrolled'
        ]));

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Delete user.
     */
    public function deleteUser(User $user)
    {
        // Don't allow deleting the current admin
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully.');
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

        return view('admin.enrollments.index', compact('enrollments'));
    }

    /**
     * Show a specific instrument borrow/enrollment.
     */
    public function showEnrollment(InstrumentBorrow $enrollment)
    {
        $enrollment->load(['user', 'instrument']);
        
        return view('admin.enrollments.show', compact('enrollment'));
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

        // Update the status with appropriate timestamps
        $updateData = ['status' => $newStatus];

        if ($newStatus === 'borrowed' && $oldStatus === 'pending') {
            $updateData['borrowed_at'] = now();
            $updateData['due_date'] = now()->addWeeks(2);
        } elseif ($newStatus === 'returned' && in_array($oldStatus, ['borrowed', 'overdue'])) {
            $updateData['returned_at'] = now();
        }

        $enrollment->update($updateData);

        return back()->with('success', 'Instrument borrow status updated successfully.');
    }

    /**
     * Manage course enrollments.
     */
    public function courseEnrollments(Request $request)
    {
        $query = CourseEnrollment::with(['user', 'course']);

        if ($request->has('search') && $request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhereHas('course', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $courseEnrollments = $query->latest()->paginate(15);

        return view('admin.course_enrollments.index', compact('courseEnrollments'));
    }

    /**
     * Update course enrollment status.
     */
    public function updateCourseEnrollmentStatus(Request $request, CourseEnrollment $courseEnrollment)
    {
        $request->validate([
            'status' => ['required', 'in:pending,active,completed,dropped'],
        ]);

        $oldStatus = $courseEnrollment->status;
        $newStatus = $request->status;

        $updateData = ['status' => $newStatus];

        if ($newStatus === 'active' && $oldStatus === 'pending') {
            $updateData['enrolled_at'] = now();
        } elseif ($newStatus === 'completed' && $oldStatus === 'active') {
            $updateData['completed_at'] = now();
        }

        $courseEnrollment->update($updateData);

        return back()->with('success', 'Course enrollment status updated successfully.');
    }

    /**
     * Manage instruments (admin access to instrument management).
     */
    public function instruments()
    {
        return redirect()->route('instruments.index');
    }
}