<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'type',
        'message',
        'is_read',
        'replied_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'replied_at' => 'datetime',
    ];
}
