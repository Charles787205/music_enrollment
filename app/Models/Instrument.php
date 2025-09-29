<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Instrument extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'category',
        'difficulty_level',
        'rental_fee',
        'is_available',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rental_fee' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    /**
     * Get the instrument borrows for this instrument.
     */
    public function instrumentBorrows()
    {
        return $this->hasMany(InstrumentBorrow::class);
    }

    /**
     * Get the enrollments for this instrument (legacy - deprecated).
     * @deprecated Use instrumentBorrows() instead
     */
    public function enrollments()
    {
        return $this->instrumentBorrows();
    }

    /**
     * Get the users who have borrowed this instrument.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'instrument_borrows')
                    ->withPivot('status', 'borrowed_at', 'due_date', 'returned_at', 'notes')
                    ->withTimestamps();
    }

    /**
     * Get only available instruments.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Get instruments by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get instruments by difficulty level.
     */
    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }
}
