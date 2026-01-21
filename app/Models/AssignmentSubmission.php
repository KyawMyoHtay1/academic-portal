<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory;

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_GRADED = 'graded';
    public const STATUS_RETURNED = 'returned';

    protected $fillable = [
        'assignment_id',
        'student_id',
        'file_path',
        'original_filename',
        'comments',
        'score',
        'feedback',
        'graded_by',
        'graded_at',
        'status',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'graded_at' => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function isGraded(): bool
    {
        return in_array($this->status, [self::STATUS_GRADED, self::STATUS_RETURNED]) && $this->score !== null;
    }

    public function getPercentageAttribute(): ?float
    {
        if (!$this->score || !$this->assignment) {
            return null;
        }

        return round(($this->score / $this->assignment->max_score) * 100, 2);
    }
}
