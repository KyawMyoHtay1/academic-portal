<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
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

    /**
     * Convert numeric score to letter grade.
     * Grading scale:
     * A: 80-100
     * B: 70-79
     * C: 60-69
     * D: 50-59
     * E: 40-49
     * F: 1-39
     * 
     * @return string|null Letter grade or null if score is null
     */
    public function getLetterGradeAttribute(): ?string
    {
        if ($this->score === null) {
            return null;
        }

        $score = (float) $this->score;

        if ($score >= 80) {
            return 'A';
        } elseif ($score >= 70) {
            return 'B';
        } elseif ($score >= 60) {
            return 'C';
        } elseif ($score >= 50) {
            return 'D';
        } elseif ($score >= 40) {
            return 'E';
        } elseif ($score >= 1) {
            return 'F';
        }
        
        return null;
    }
}
