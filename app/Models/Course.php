<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'title',
        'credits',
        'semester',
        'photo',
    ];

    /**
     * The students enrolled in this course.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_student')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * The teachers assigned to this course.
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'course_teacher', 'course_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * The attendance records for this course (via subjects). Attendance has subject_id only.
     */
    public function attendances()
    {
        return $this->hasManyThrough(Attendance::class, Subject::class, 'course_id', 'subject_id', 'id', 'id');
    }

    /**
     * The grades recorded for this course.
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * The timetable entries for this course (via subjects). Timetable has subject_id only.
     */
    public function timetables()
    {
        return $this->hasManyThrough(Timetable::class, Subject::class, 'course_id', 'subject_id', 'id', 'id');
    }

    /**
     * The subjects that belong to this course.
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * The assignments for this course.
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}


