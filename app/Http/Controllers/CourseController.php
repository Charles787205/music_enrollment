<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::with(['enrolledStudents'])
            ->where('status', '!=', 'inactive')
            ->paginate(12);
            
        return view('courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->hasStaffAccess()) {
            abort(403);
        }
        return view('courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasStaffAccess()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'max_students' => 'required|integer|min:1|max:200',
            'price' => 'required|numeric|min:0',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'instructor' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('courses', 'public');
        }

        Course::create($validated);

        return redirect()->route('courses.index')
            ->with('success', 'Course created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course->load(['enrolledStudents']);
        $isEnrolled = Auth::check() ? $course->isUserEnrolled(Auth::user()) : false;
        
        return view('courses.show', compact('course', 'isEnrolled'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        if (!Auth::user()->hasStaffAccess()) {
            abort(403);
        }
        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        if (!Auth::user()->hasStaffAccess()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'max_students' => 'required|integer|min:1|max:200',
            'price' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'instructor' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($course->image && Storage::disk('public')->exists($course->image)) {
                Storage::disk('public')->delete($course->image);
            }
            $validated['image'] = $request->file('image')->store('courses', 'public');
        }

        $course->update($validated);

        return redirect()->route('courses.index')
            ->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        if (!Auth::user()->hasStaffAccess()) {
            abort(403);
        }
        
        // Delete image if exists
        if ($course->image && Storage::disk('public')->exists($course->image)) {
            Storage::disk('public')->delete($course->image);
        }
        
        $course->delete();

        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully!');
    }

    /**
     * Enroll user in course
     */
    public function enroll(Course $course)
    {
        $user = Auth::user();

        if (!$user || !$user->isStudent()) {
            return redirect()->back()->with('error', 'Only students can enroll in courses.');
        }

        // Check if user is already enrolled
        $existingEnrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', '!=', 'dropped')
            ->first();

        if ($existingEnrollment) {
            return redirect()->back()->with('error', 'You are already enrolled in this course.');
        }

        if ($course->isFull()) {
            return redirect()->back()->with('error', 'This course is full.');
        }

        if ($course->status !== 'active') {
            return redirect()->back()->with('error', 'This course is not available for enrollment.');
        }

        CourseEnrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'pending', // Start as pending, staff can approve
        ]);

        return redirect()->back()->with('success', 'Enrollment request submitted! Please wait for approval.');
    }

    /**
     * Unenroll user from course
     */
    public function unenroll(Course $course)
    {
        $user = Auth::user();

        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', '!=', 'dropped')
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'You are not enrolled in this course.');
        }

        $enrollment->update(['status' => 'dropped']);

        return redirect()->back()->with('success', 'Successfully unenrolled from the course.');
    }
}
    
