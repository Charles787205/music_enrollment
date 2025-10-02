<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'date_of_birth',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'user_type',
        'is_enrolled',
        'password_change_required',
        'is_approved',
        'approved_at',
        'approved_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'is_enrolled' => 'boolean',
            'is_approved' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Get the instrument borrows for the user.
     */
    public function instrumentBorrows()
    {
        return $this->hasMany(InstrumentBorrow::class);
    }

    /**
     * Get the enrollments for the user (legacy - deprecated).
     * @deprecated Use instrumentBorrows() instead
     */
    public function enrollments()
    {
        return $this->hasMany(InstrumentBorrow::class);
    }

    /**
     * Get the instruments this user has borrowed.
     */
    public function instruments()
    {
        return $this->belongsToMany(Instrument::class, 'instrument_borrows')
                    ->withPivot('status', 'start_date', 'end_date', 'notes', 'total_fee')
                    ->withTimestamps();
    }

    /**
     * Get the courses this user is enrolled in.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_enrollments')
                    ->withPivot(['status', 'enrolled_at', 'completed_at'])
                    ->withTimestamps();
    }

    /**
     * Get the course enrollments for this user.
     */
    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    /**
     * Get the course enrollments where this user is the teacher.
     */
    public function teachingEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class, 'teacher_id');
    }

    /**
     * Get the students this teacher is teaching.
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'course_enrollments', 'teacher_id', 'user_id')
                    ->withPivot(['status', 'enrolled_at', 'completed_at', 'grade'])
                    ->withTimestamps();
    }

    /**
     * Get payments collected by this employee.
     */
    public function collectedPayments()
    {
        return $this->hasMany(CourseEnrollment::class, 'collected_by');
    }

    /**
     * Get active course enrollments
     */
    public function activeCourses()
    {
        return $this->courses()->wherePivot('status', 'active');
    }

    /**
     * Check if user is a student.
     */
    public function isStudent()
    {
        return $this->user_type === 'student';
    }

    /**
     * Check if user is an employee.
     */
    public function isEmployee()
    {
        return $this->user_type === 'employee';
    }

    /**
     * Check if user is a teacher.
     */
    public function isTeacher()
    {
        return $this->user_type === 'teacher';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    /**
     * Check if user is currently borrowing a specific instrument.
     */
    public function isBorrowingInstrument($instrumentId)
    {
        return $this->instrumentBorrows()
            ->where('instrument_id', $instrumentId)
            ->whereIn('status', ['pending', 'borrowed'])
            ->exists();
    }

    /**
     * Check if user is enrolled in a specific course.
     */
    public function isEnrolledInCourse($courseId)
    {
        return $this->courses()
            ->where('course_id', $courseId)
            ->wherePivot('status', 'enrolled')
            ->exists();
    }

    /**
     * Check if user has admin or employee privileges.
     */
    public function hasStaffAccess()
    {
        return in_array($this->user_type, ['admin', 'employee']);
    }

    /**
     * Get the user who approved this account.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the users this user has approved.
     */
    public function approvedUsers()
    {
        return $this->hasMany(User::class, 'approved_by');
    }

    /**
     * Check if the user account is approved.
     */
    public function isApproved()
    {
        return $this->is_approved;
    }

    /**
     * Check if this is the first admin (for initial setup).
     */
    public static function hasAdmins()
    {
        return self::where('user_type', 'admin')->where('is_approved', true)->exists();
    }
}
