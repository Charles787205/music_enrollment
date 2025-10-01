<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'bio',
        'specialization',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the courses taught by this teacher.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the course enrollments for courses taught by this teacher.
     */
    public function courseEnrollments()
    {
        return $this->hasManyThrough(CourseEnrollment::class, Course::class);
    }

    /**
     * Scope to get only active teachers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
