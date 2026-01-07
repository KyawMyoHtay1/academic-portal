<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'student_id',
        'graded_by',
        'score',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    /**
     * The course this grade belongs to.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * The student this grade belongs to.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * The user (teacher) who recorded this grade.
     */
    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
