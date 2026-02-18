<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAYMENT_PENDING = 'payment_pending';

    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'student_id',
        'amount',
        'description',
        'status',
        'due_date',
        'paid_date',
        'payment_intent_id',
        'payment_method',
        'payment_processed_at',
        'processed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
        'payment_processed_at' => 'datetime',
    ];

    /**
     * The student this fee belongs to.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * The user (staff) who processed/approved the payment.
     */
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function statusLogs()
    {
        return $this->hasMany(FeeStatusLog::class)
            ->with('performer:id,name')
            ->orderByDesc('created_at');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaymentPending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PAYMENT_PENDING);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query
            ->pending()
            ->where('due_date', '<', now()->toDateString());
    }

    public function markAsPaymentPending(
        ?string $paymentIntentId = null,
        ?int $performedBy = null,
        string $action = 'payment_pending',
        ?string $note = null,
        array $meta = []
    ): void
    {
        $fromStatus = $this->status;

        $this->update([
            'status' => self::STATUS_PAYMENT_PENDING,
            'payment_intent_id' => $paymentIntentId ?? $this->payment_intent_id,
        ]);

        $this->logStatusChange(
            $fromStatus,
            self::STATUS_PAYMENT_PENDING,
            $action,
            $performedBy,
            $note,
            $meta
        );
    }

    public function markAsPending(
        ?int $performedBy = null,
        string $action = 'pending',
        ?string $note = null,
        array $meta = []
    ): void
    {
        $fromStatus = $this->status;

        $this->update([
            'status' => self::STATUS_PENDING,
            'paid_date' => null,
            'payment_intent_id' => null,
            'payment_method' => null,
            'payment_processed_at' => null,
            'processed_by' => null,
        ]);

        $this->logStatusChange(
            $fromStatus,
            self::STATUS_PENDING,
            $action,
            $performedBy,
            $note,
            $meta
        );
    }

    public function markAsPaid(
        ?string $paymentMethod = null,
        ?string $paymentIntentId = null,
        ?int $processedBy = null,
        string $action = 'paid',
        ?string $note = null,
        array $meta = []
    ): void
    {
        $fromStatus = $this->status;

        $this->update([
            'status' => self::STATUS_PAID,
            'paid_date' => now()->toDateString(),
            'payment_method' => $paymentMethod,
            'payment_processed_at' => now(),
            'payment_intent_id' => $paymentIntentId ?? $this->payment_intent_id,
            'processed_by' => $processedBy,
        ]);

        $this->logStatusChange(
            $fromStatus,
            self::STATUS_PAID,
            $action,
            $processedBy,
            $note,
            $meta
        );
    }

    public function logStatusChange(
        ?string $fromStatus,
        ?string $toStatus,
        string $action,
        ?int $performedBy = null,
        ?string $note = null,
        array $meta = []
    ): void
    {
        FeeStatusLog::create([
            'fee_id' => $this->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'action' => $action,
            'note' => $note,
            'performed_by' => $performedBy,
            'meta' => $meta === [] ? null : $meta,
        ]);
    }
}
