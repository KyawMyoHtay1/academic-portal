<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'subject_id',
        'course_id',
        'student_id',
        'graded_by',
        'reviewed_by',
        'score',
        'status',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'reviewed_at' => 'datetime',
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
     * The user (staff/admin) who reviewed/approved/rejected this grade.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function reviewLogs()
    {
        return $this->hasMany(GradeReviewLog::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function submitForReview(float $score, int $gradedBy, int $courseId): void
    {
        $this->update([
            'course_id' => $courseId,
            'graded_by' => $gradedBy,
            'score' => $score,
            'status' => self::STATUS_PENDING,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'rejection_reason' => null,
        ]);
    }

    public function approve(int $reviewedBy): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    public function reject(int $reviewedBy, ?string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
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
