<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration',
        'fee_per_month'
    ];

    protected $casts = [
        'fee_per_month' => 'decimal:2',
    ];

    /**
     * Get the students enrolled in this course.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_student')
                    ->withPivot('enrolled_at')
                    ->withTimestamps();
    }

    /**
     * Get all payments for this course.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
