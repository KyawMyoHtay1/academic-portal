<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LowAttendanceAlertState extends Model
{
    use HasFactory;

    protected $table = 'low_attendance_alert_states';

    protected $fillable = [
        'student_id',
        'last_rate',
        'is_below_threshold',
        'last_alert_sent_at',
    ];

    protected $casts = [
        'last_rate' => 'decimal:2',
        'is_below_threshold' => 'boolean',
        'last_alert_sent_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}

