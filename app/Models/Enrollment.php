<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enrollment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'instrument_id',
        'status',
        'start_date',
        'end_date',
        'notes',
        'total_fee',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_fee' => 'decimal:2',
    ];

    /**
     * Get the user that owns the enrollment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the instrument for this enrollment.
     */
    public function instrument()
    {
        return $this->belongsTo(Instrument::class);
    }

    /**
     * Scope for active enrollments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for pending enrollments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if enrollment is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if enrollment is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
}
