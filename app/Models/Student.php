<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'dob',
        'address',
        'status'
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    /**
     * Get the courses assigned to the student.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_student')
                    ->withPivot('enrolled_at')
                    ->withTimestamps();
    }

    /**
     * Get all payments made by the student.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Calculate total fee for all assigned courses.
     */
    public function getTotalFeeAttribute()
    {
        return $this->courses->sum('fee_per_month');
    }

    /**
     * Calculate total amount paid by the student.
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments->sum('amount_paid');
    }

    /**
     * Calculate remaining balance.
     */
    public function getBalanceAttribute()
    {
        return $this->total_fee - $this->total_paid;
    }
}
