<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'subject_id',
        'created_by',
        'title',
        'description',
        'due_date',
        'due_time',
        'max_score',
        'status',
        'allowed_file_types',
        'max_file_size',
    ];

    protected $casts = [
        'due_date' => 'date',
        'allowed_file_types' => 'array',
        'max_file_size' => 'integer',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function getCourseAttribute(): ?Course
    {
        return $this->subject?->course;
    }

    public function getCourseIdAttribute(): ?int
    {
        return $this->subject?->course_id;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function isOverdue(): bool
    {
        if (! $this->due_date) {
            return false;
        }

        $dueDateTime = $this->due_date->copy()->endOfDay();
        if ($this->due_time) {
            $timeParts = explode(':', $this->due_time);
            if (count($timeParts) >= 2) {
                $dueDateTime = $this->due_date->copy()->setTime((int) $timeParts[0], (int) $timeParts[1], 0);
            }
        }

        return now()->isAfter($dueDateTime);
    }

    public function canSubmit(): bool
    {
        return $this->status === self::STATUS_PUBLISHED && ! $this->isOverdue();
    }
}
