<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'amount',
        'description',
        'status',
        'due_date',
        'paid_date',
        'payment_intent_id',
        'payment_method',
        'payment_processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
        'payment_processed_at' => 'datetime',
    ];

    /**
     * The student this fee belongs to.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
