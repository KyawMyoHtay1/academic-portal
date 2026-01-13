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
    ];

    /**
     * The course this subject belongs to.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
