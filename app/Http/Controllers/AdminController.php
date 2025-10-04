<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Instrument;
use App\Models\InstrumentBorrow;
use App\Models\CourseEnrollment;
use App\Models\Course;
use App\Models\Teacher;
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
            'pending_users' => User::where('is_approved', false)->whereIn('user_type', ['admin', 'employee'])->count(),
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
            'status' => ['required', 'in:pending,enrolled,completed,dropped'],
        ]);

        $oldStatus = $courseEnrollment->status;
        $newStatus = $request->status;

        $updateData = ['status' => $newStatus];

        if ($newStatus === 'enrolled' && $oldStatus === 'pending') {
            $updateData['enrolled_at'] = now();
        } elseif ($newStatus === 'completed' && $oldStatus === 'enrolled') {
            $updateData['completed_at'] = now();
        }

        $courseEnrollment->update($updateData);

        return back()->with('success', 'Course enrollment status updated successfully.');
    }

    /**
     * Show a specific course enrollment.
     */
    public function showCourseEnrollment(CourseEnrollment $courseEnrollment)
    {
        $courseEnrollment->load(['user', 'course.teacher']);
        
        return view('admin.course_enrollments.show', compact('courseEnrollment'));
    }

    /**
     * Show course management page.
     */
    public function courses(Request $request)
    {
        $query = Course::with(['teacher', 'enrollments.user']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $courses = $query->paginate(15);

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show specific course details for management.
     */
    public function showCourse(Course $course)
    {
        $course->load(['teacher', 'enrollments.user']);
        
        // Get all available teachers
        $teachers = Teacher::active()->orderBy('name')->get();
        
        // Get students not enrolled in this course
        $enrolledStudentIds = $course->enrollments->pluck('user_id')->toArray();
        $availableStudents = User::where('user_type', 'student')
            ->whereNotIn('id', $enrolledStudentIds)
            ->orderBy('name')
            ->get();

        return view('admin.courses.show', compact('course', 'teachers', 'availableStudents'));
    }

    /**
     * Update course teacher assignment.
     */
    public function updateCourseTeacher(Request $request, Course $course)
    {
        $request->validate([
            'teacher_id' => 'nullable|exists:teachers,id'
        ]);

        $course->update(['teacher_id' => $request->teacher_id]);

        $message = $request->teacher_id 
            ? 'Course teacher updated successfully.'
            : 'Course teacher assignment removed successfully.';

        return back()->with('success', $message);
    }

    /**
     * Assign a student to a course.
     */
    public function assignStudent(Request $request, Course $course)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'total_fee' => 'required|numeric|min:0',
            'payment_due_date' => 'nullable|date|after_or_equal:today',
        ]);

        // Verify the user is a student
        $student = User::findOrFail($request->student_id);
        if (!$student->isStudent()) {
            return back()->with('error', 'Selected user is not a student.');
        }

        // Check if student is already enrolled
        $existingEnrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', $student->id)
            ->first();

        if ($existingEnrollment) {
            return back()->with('error', 'Student is already enrolled in this course.');
        }

        // Create the enrollment with default pending status
        CourseEnrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'teacher_id' => $course->teacher_id,
            'status' => 'pending', // Default status
            'total_fee' => $request->total_fee,
            'amount_paid' => 0,
            'payment_status' => 'pending',
            'payment_due_date' => $request->payment_due_date,
            'enrolled_at' => $request->status === 'active' ? now() : null,
        ]);

        return back()->with('success', 'Student assigned to course successfully.');
    }

    /**
     * Remove a student from a course.
     */
    public function removeStudent(Course $course, User $student)
    {
        $enrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', $student->id)
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'Student is not enrolled in this course.');
        }

        $enrollment->delete();

        return back()->with('success', 'Student removed from course successfully.');
    }

    /**
     * Show teachers management page.
     */
    public function teachers(Request $request)
    {
        $query = Teacher::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('specialization', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $teachers = $query->orderBy('name')->paginate(15);

        return view('admin.teachers.index', compact('teachers'));
    }

    /**
     * Show create teacher form.
     */
    public function createTeacher()
    {
        return view('admin.teachers.create');
    }

    /**
     * Store new teacher.
     */
    public function storeTeacher(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:teachers,email',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'specialization' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        Teacher::create($request->all());

        return redirect()->route('admin.teachers')->with('success', 'Teacher created successfully.');
    }

    /**
     * Show edit teacher form.
     */
    public function editTeacher(Teacher $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    /**
     * Update teacher.
     */
    public function updateTeacher(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:teachers,email,' . $teacher->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'specialization' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $teacher->update($request->all());

        return redirect()->route('admin.teachers')->with('success', 'Teacher updated successfully.');
    }

    /**
     * Delete teacher.
     */
    public function deleteTeacher(Teacher $teacher)
    {
        // Check if teacher has any assigned courses
        if ($teacher->courses()->count() > 0) {
            return back()->with('error', 'Cannot delete teacher with assigned courses. Please reassign courses first.');
        }

        $teacher->delete();

        return back()->with('success', 'Teacher deleted successfully.');
    }

    /**
     * Manage instruments (admin access to instrument management).
     */
    public function instruments()
    {
        return redirect()->route('instruments.index');
    }

    /**
     * Show pending user approvals.
     */
    public function pendingUsers()
    {
        $pendingUsers = User::where('is_approved', false)
            ->whereIn('user_type', ['admin', 'employee'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.pending-users', compact('pendingUsers'));
    }

    /**
     * Approve a user.
     */
    public function approveUser(User $user)
    {
        if ($user->is_approved) {
            return back()->with('warning', 'User is already approved.');
        }

        if (!in_array($user->user_type, ['admin', 'employee'])) {
            return back()->with('error', 'Only admin and employee accounts require approval.');
        }

        $user->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', "User {$user->name} has been approved successfully.");
    }

    /**
     * Reject a user.
     */
    public function rejectUser(User $user)
    {
        if ($user->is_approved) {
            return back()->with('warning', 'Cannot reject an already approved user.');
        }

        if (!in_array($user->user_type, ['admin', 'employee'])) {
            return back()->with('error', 'Only admin and employee accounts require approval.');
        }

        // Delete the user instead of just marking as rejected
        $userName = $user->name;
        $user->delete();

        return back()->with('success', "User registration for {$userName} has been rejected and removed.");
    }
}