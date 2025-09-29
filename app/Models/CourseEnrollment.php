<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'enrolled_at',
        'completed_at',
        'grade',
        'notes',
    ];

    protected $casts = [
        'enrolled_at' => 'date',
        'completed_at' => 'date',
        'grade' => 'decimal:2',
    ];

    /**
     * Get the user who enrolled in the course.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course for this enrollment.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Check if the enrollment is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if the enrollment is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the enrollment is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Get a formatted grade display.
     */
    public function getFormattedGradeAttribute()
    {
        if ($this->grade === null) {
            return 'Not graded';
        }
        return number_format($this->grade, 1) . '%';
    }

    /**
     * Get status badge class for styling.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'active' => 'success',
            'completed' => 'info',
            'dropped' => 'secondary',
            default => 'secondary'
        };
    }
}
