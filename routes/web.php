<?php

use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\InstrumentController;
use App\Http\Controllers\InstrumentBorrowController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseEnrollmentController;
use App\Http\Controllers\GuestEnrollmentController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Landing page for all visitors
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif (auth()->user()->isEmployee()) {
            return redirect()->route('employee.dashboard');
        } else {
            return redirect()->route('courses.index');
        }
    }
    return view('welcome');
})->name('home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Change Routes (for authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [PasswordController::class, 'show'])->name('password.change');
    Route::post('/change-password', [PasswordController::class, 'update'])->name('password.update')->withoutMiddleware(['password.required']);
});

// Public routes - these work without authentication
Route::get('/instruments', [InstrumentController::class, 'index'])->name('instruments.index');

// Guest enrollment routes (no authentication required)
Route::get('/enroll', [GuestEnrollmentController::class, 'create'])->name('guest.enroll');
Route::get('/enroll/{course}', [GuestEnrollmentController::class, 'create'])->name('guest.enroll.course');
Route::post('/enroll', [GuestEnrollmentController::class, 'store'])->name('guest.enroll.store');
Route::get('/enrollment-success', [GuestEnrollmentController::class, 'success'])->name('guest.enroll.success');

// Admin instrument management routes (must be BEFORE parameterized routes)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/instruments/create', [InstrumentController::class, 'create'])->name('instruments.create');
    Route::post('/instruments', [InstrumentController::class, 'store'])->name('instruments.store');
    Route::get('/instruments/{instrument}/edit', [InstrumentController::class, 'edit'])->name('instruments.edit');
    Route::put('/instruments/{instrument}', [InstrumentController::class, 'update'])->name('instruments.update');
    Route::delete('/instruments/{instrument}', [InstrumentController::class, 'destroy'])->name('instruments.destroy');
});

Route::get('/instruments/{instrument}', [InstrumentController::class, 'show'])->name('instruments.show');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Enrollment Management
    Route::get('/enrollments', [AdminController::class, 'enrollments'])->name('enrollments');
    Route::get('/enrollments/{enrollment}', [AdminController::class, 'showEnrollment'])->name('enrollments.show');
    Route::patch('/enrollments/{enrollment}/status', [AdminController::class, 'updateEnrollmentStatus'])->name('enrollments.update-status');
    
    // Course Management
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::get('/courses/{course}', [AdminController::class, 'showCourse'])->name('courses.show');
    Route::put('/courses/{course}/teacher', [AdminController::class, 'updateCourseTeacher'])->name('courses.update-teacher');
    Route::post('/courses/{course}/assign-student', [AdminController::class, 'assignStudent'])->name('courses.assign-student');
    Route::delete('/courses/{course}/students/{student}', [AdminController::class, 'removeStudent'])->name('courses.remove-student');
    
    // Teacher Management
    Route::get('/teachers', [AdminController::class, 'teachers'])->name('teachers');
    Route::get('/teachers/create', [AdminController::class, 'createTeacher'])->name('teachers.create');
    Route::post('/teachers', [AdminController::class, 'storeTeacher'])->name('teachers.store');
    Route::get('/teachers/{teacher}/edit', [AdminController::class, 'editTeacher'])->name('teachers.edit');
    Route::put('/teachers/{teacher}', [AdminController::class, 'updateTeacher'])->name('teachers.update');
    Route::delete('/teachers/{teacher}', [AdminController::class, 'deleteTeacher'])->name('teachers.delete');
    
    // Payment Reports
    Route::get('/payments/reports', [PaymentController::class, 'reports'])->name('payments.reports');
});

// Employee Routes
Route::middleware(['auth', 'employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
    
    // Student Management
    Route::get('/students', [EmployeeController::class, 'students'])->name('students');
    Route::get('/students/{student}', [EmployeeController::class, 'showStudent'])->name('students.show');
    
    // Instrument Borrow Management
    Route::get('/enrollments', [EmployeeController::class, 'enrollments'])->name('enrollments');
    Route::patch('/enrollments/{enrollment}/status', [EmployeeController::class, 'updateEnrollmentStatus'])->name('enrollments.update-status');
    
    // Course Enrollment Management
    Route::get('/course-enrollments', [EmployeeController::class, 'courseEnrollments'])->name('course-enrollments');
    Route::patch('/course-enrollments/{courseEnrollment}/status', [EmployeeController::class, 'updateCourseEnrollmentStatus'])->name('course-enrollments.update-status');
    
    // Payment Collection
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments');
    Route::get('/payments/{enrollment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{enrollment}/collect', [PaymentController::class, 'collect'])->name('payments.collect');
    Route::get('/payments/{enrollment}/history', [PaymentController::class, 'history'])->name('payments.history');
});

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Course routes - for course enrollment
    Route::resource('courses', CourseController::class);
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::delete('/courses/{course}/unenroll', [CourseController::class, 'unenroll'])->name('courses.unenroll');
    
    // Course enrollment management routes
    Route::resource('course-enrollments', CourseEnrollmentController::class)->only(['index', 'show']);
    
    // Instrument borrowing routes - separate from course enrollment
    Route::resource('instrument-borrows', InstrumentBorrowController::class);
});

// Test route to debug admin access
Route::get('/test-admin', function () {
    if (!auth()->check()) {
        return response()->json(['error' => 'Not authenticated']);
    }
    
    $user = auth()->user();
    return response()->json([
        'authenticated' => true,
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'user_type' => $user->user_type,
        'is_admin' => $user->isAdmin(),
    ]);
});