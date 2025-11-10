<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'amount_paid',
        'date_of_payment'
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'date_of_payment' => 'date',
    ];

    /**
     * Get the student that made the payment.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course the payment is for.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
