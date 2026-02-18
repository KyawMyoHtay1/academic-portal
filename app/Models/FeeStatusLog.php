<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_id',
        'from_status',
        'to_status',
        'action',
        'note',
        'performed_by',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
