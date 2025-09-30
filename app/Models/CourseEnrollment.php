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
        'teacher_id',
        'status',
        'enrolled_at',
        'completed_at',
        'grade',
        'notes',
        'total_fee',
        'amount_paid',
        'payment_status',
        'payment_due_date',
        'collected_by',
        'payment_collected_at',
        'payment_notes',
    ];

    protected $casts = [
        'enrolled_at' => 'date',
        'completed_at' => 'date',
        'payment_due_date' => 'datetime',
        'payment_collected_at' => 'datetime',
        'grade' => 'decimal:2',
        'total_fee' => 'decimal:2',
        'amount_paid' => 'decimal:2',
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
     * Get the teacher assigned to this enrollment.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the employee who collected the payment.
     */
    public function collector()
    {
        return $this->belongsTo(User::class, 'collected_by');
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
     * Check if payment is fully paid.
     */
    public function isFullyPaid()
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if payment is overdue.
     */
    public function isPaymentOverdue()
    {
        return $this->payment_due_date && 
               $this->payment_due_date->isPast() && 
               !$this->isFullyPaid();
    }

    /**
     * Get remaining balance.
     */
    public function getRemainingBalance()
    {
        return $this->total_fee - $this->amount_paid;
    }

    /**
     * Get payment completion percentage.
     */
    public function getPaymentPercentage()
    {
        if (!$this->total_fee || $this->total_fee == 0) {
            return 100;
        }
        return min(100, ($this->amount_paid / $this->total_fee) * 100);
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
