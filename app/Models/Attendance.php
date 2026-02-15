<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'student_id',
        'date',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * The course this attendance record belongs to (via subject). Subject → Course.
     */
    public function course()
    {
        return $this->hasOneThrough(Course::class, Subject::class, 'id', 'id', 'subject_id', 'course_id');
    }

    /**
     * The student this attendance record belongs to.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * The subject this attendance record belongs to.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
