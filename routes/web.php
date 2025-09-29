<?php

use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\InstrumentController;
use App\Http\Controllers\InstrumentBorrowController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseEnrollmentController;
use Illuminate\Support\Facades\Route;

// Redirect root to login for unauthenticated users, otherwise to appropriate dashboard
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
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public routes - these work without authentication
Route::get('/instruments', [InstrumentController::class, 'index'])->name('instruments.index');

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
    Route::patch('/enrollments/{enrollment}/status', [AdminController::class, 'updateEnrollmentStatus'])->name('enrollments.update-status');
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