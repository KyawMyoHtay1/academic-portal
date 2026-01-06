<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_no',
        'full_name',
        'dob',
        'email',
        'phone',
        'programme',
        'intake_year',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The courses that the student is enrolled in.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_student')
            ->withTimestamps();
    }
}


