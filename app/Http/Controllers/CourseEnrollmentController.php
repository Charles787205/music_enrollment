<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseEnrollmentController extends Controller
{
    /**
     * Display a listing of the user's course enrollments.
     */
    public function index()
    {
        $enrollments = CourseEnrollment::where('user_id', Auth::id())
            ->with(['course', 'teacher'])
            ->latest()
            ->paginate(10);

        return view('course_enrollments.index', compact('enrollments'));
    }

    /**
     * Display the specified course enrollment.
     */
    public function show(CourseEnrollment $courseEnrollment)
    {
        // Check if the authenticated user owns this enrollment
        if ($courseEnrollment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this enrollment.');
        }

        $courseEnrollment->load('course');
        return view('course_enrollments.show', compact('courseEnrollment'));
    }
}
