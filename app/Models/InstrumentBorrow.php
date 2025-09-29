<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstrumentBorrow extends Model
{
    use HasFactory;

    protected $table = 'instrument_borrows';

    protected $fillable = [
        'user_id',
        'instrument_id',
        'status',
        'notes',
        'borrowed_at',
        'due_date',
        'returned_at',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /**
     * Get the user who borrowed the instrument.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the instrument that was borrowed.
     */
    public function instrument()
    {
        return $this->belongsTo(Instrument::class);
    }

    /**
     * Check if the borrow is overdue.
     */
    public function isOverdue()
    {
        return $this->status === 'borrowed' && 
               $this->due_date && 
               $this->due_date->isPast();
    }

    /**
     * Check if the borrow is active (borrowed).
     */
    public function isActive()
    {
        return $this->status === 'borrowed';
    }
}
