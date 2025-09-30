<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GuestEnrollmentController extends Controller
{
    /**
     * Show guest enrollment form.
     */
    public function create(Course $course = null)
    {
        $courses = Course::where('status', 'active')
            ->where('current_enrolled', '<', DB::raw('max_students'))
            ->get();

        return view('guest-enrollment.create', compact('courses'))
            ->with('selectedCourse', $course);
    }

    /**
     * Process guest enrollment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'nullable|string|max:500',
            'course_id' => 'required|exists:courses,id',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_relationship' => 'required|string|in:parent,guardian,spouse,sibling,friend,other',
            'previous_experience' => 'nullable|string|max:1000',
            'goals' => 'nullable|string|max:500',
            'terms_accepted' => 'required|accepted',
        ]);

        $course = Course::findOrFail($request->course_id);

        // Check if course is still available
        if ($course->current_enrolled >= $course->max_students) {
            return back()->withErrors(['course_id' => 'This course is now full.'])->withInput();
        }

        // Check if user already exists with this email
        $user = User::where('email', $request->email)->first();
        $isNewUser = false;

        if (!$user) {
            // Create a temporary user account without password (they'll set it up later)
            $tempPassword = Str::random(8); // Generate a simple temporary password
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'user_type' => 'student',
                'is_enrolled' => false,
                'password' => Hash::make($tempPassword), // Temporary password they can use
                'password_change_required' => true, // Force password change on first login
                'email_verified_at' => null, // Not verified yet
            ]);
            $isNewUser = true;
        }

        // Check if already enrolled in this course
        $existingEnrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereIn('status', ['pending', 'active'])
            ->first();

        if ($existingEnrollment) {
            return back()->withErrors(['course_id' => 'You are already enrolled in this course.'])->withInput();
        }

        // Create course enrollment
        $enrollment = CourseEnrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'pending',
            'enrolled_at' => now(),
            'total_fee' => $course->price,
            'amount_paid' => 0,
            'payment_status' => 'pending',
            'payment_due_date' => now()->addDays(30), // 30 days to pay
            'notes' => $this->formatEnrollmentNotes($request),
        ]);

        // Update course enrollment count
        $course->increment('current_enrolled');

        // Load relationships for the enrollment
        $enrollment->load(['user', 'course', 'teacher']);

        // Send success message with enrollment details
        return redirect()->route('guest.enroll.success')
            ->with('enrollment', $enrollment)
            ->with('course', $course)
            ->with('user', $user)
            ->with('isNewUser', $isNewUser)
            ->with('tempPassword', $isNewUser ? $tempPassword : null);
    }

    /**
     * Show enrollment success page with account creation option.
     */
    public function success()
    {
        return view('guest-enrollment.success');
    }

    /**
     * Format enrollment notes from form data.
     */
    private function formatEnrollmentNotes(Request $request)
    {
        $notes = [];
        
        if ($request->filled('previous_experience')) {
            $notes[] = 'Previous Experience: ' . $request->previous_experience;
        }
        
        if ($request->filled('goals')) {
            $notes[] = 'Goals: ' . $request->goals;
        }
        
        if ($request->filled('emergency_contact_relationship')) {
            $notes[] = 'Emergency Contact Relationship: ' . ucfirst($request->emergency_contact_relationship);
        }
        
        return implode("\n\n", $notes);
    }
}