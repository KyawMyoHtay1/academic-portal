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
     * Access via subject to avoid hasOneThrough key mapping; eager load subject.course to avoid N+1.
     */
    public function getCourseAttribute(): ?Course
    {
        return $this->subject?->course;
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
