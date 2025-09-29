<?php

namespace App\Models;

class Student extends User
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('students', function ($builder) {
            $builder->where('user_type', 'student');
        });
    }

    /**
     * Get the student's age.
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Get active enrollments for the student.
     */
    public function activeEnrollments()
    {
        return $this->enrollments()->active();
    }

    /**
     * Get pending enrollments for the student.
     */
    public function pendingEnrollments()
    {
        return $this->enrollments()->pending();
    }

    /**
     * Check if student is enrolled in a specific instrument.
     */
    public function isEnrolledIn($instrumentId)
    {
        return $this->enrollments()
                    ->where('instrument_id', $instrumentId)
                    ->whereIn('status', ['active', 'pending'])
                    ->exists();
    }
}
