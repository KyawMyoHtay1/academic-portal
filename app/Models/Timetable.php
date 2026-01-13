<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'subject_id',
        'day_of_week',
        'start_time',
        'end_time',
        'location',
    ];

    /**
     * The course this timetable entry belongs to (derived from subject).
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * The subject this timetable entry belongs to.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
