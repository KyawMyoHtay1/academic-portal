<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeReviewLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_id',
        'performed_by',
        'action',
        'reason',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
