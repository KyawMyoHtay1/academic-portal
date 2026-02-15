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
    ];

    /**
     * The course this timetable entry belongs to (via subject). Subject → Course.
     */
    public function course()
    {
        return $this->hasOneThrough(Course::class, Subject::class, 'id', 'id', 'subject_id', 'course_id');
    }

    /**
     * The subject this timetable entry belongs to.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
