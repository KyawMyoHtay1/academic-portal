<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'day_of_week',
        'start_time',
        'end_time',
        'location',
        'created_by',
    ];

    /**
     * The course this timetable entry belongs to (via subject). Subject → Course.
     * Access via subject to avoid hasOneThrough key mapping; eager load subject.course to avoid N+1.
     */
    public function getCourseAttribute(): ?Course
    {
        return $this->subject?->course;
    }

    /**
     * The subject this timetable entry belongs to.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * The user (staff) who created this timetable entry. User 1 – Timetable 0..*
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
