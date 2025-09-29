<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description', 
        'image',
        'max_students',
        'current_enrolled',
        'price',
        'status',
        'start_date',
        'end_date',
        'instructor'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2'
    ];

    /**
     * Students enrolled in this course
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_enrollments')
                    ->withPivot(['status', 'enrolled_at', 'completed_at'])
                    ->withTimestamps();
    }

    /**
     * Get the course enrollments for this course.
     */
    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    /**
     * Enrolled students (active enrollments only)
     */
    public function enrolledStudents(): BelongsToMany
    {
        return $this->students()->wherePivot('status', 'active');
    }

    /**
     * Check if course is full
     */
    public function isFull(): bool
    {
        $activeEnrollments = $this->courseEnrollments()->where('status', 'active')->count();
        return $activeEnrollments >= $this->max_students;
    }

    /**
     * Get available spots
     */
    public function availableSpots(): int
    {
        $activeEnrollments = $this->courseEnrollments()->where('status', 'active')->count();
        return max(0, $this->max_students - $activeEnrollments);
    }

    /**
     * Check if user is enrolled
     */
    public function isUserEnrolled(User $user): bool
    {
        return $this->courseEnrollments()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active'])
            ->exists();
    }

    /**
     * Update enrolled count
     */
    public function updateEnrolledCount(): void
    {
        $activeCount = $this->courseEnrollments()->where('status', 'active')->count();
        $this->current_enrolled = $activeCount;
        $this->status = $this->isFull() ? 'full' : ($this->status === 'full' ? 'active' : $this->status);
        $this->save();
    }
}
