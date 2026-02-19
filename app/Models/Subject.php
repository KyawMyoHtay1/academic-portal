<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'subject_code',
        'title',
        'credits',
        'description',
        'photo',
        'attendance_threshold',
    ];

    /**
     * The course this subject belongs to.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * The grades recorded for this subject.
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * The timetable entries for this subject.
     */
    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }

    /**
     * The teachers assigned to this subject.
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'subject_teacher', 'subject_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * The attendance records for this subject.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * The assignments for this subject.
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
