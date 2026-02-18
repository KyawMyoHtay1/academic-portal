<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'priority',
        'pinned',
        'require_ack',
        'audience',
        'publish_at',
        'expires_at',
        'user_id',
    ];

    protected $casts = [
        'pinned' => 'boolean',
        'require_ack' => 'boolean',
        'audience' => 'array',
        'publish_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reads(): HasMany
    {
        return $this->hasMany(AnnouncementRead::class);
    }

    /**
     * Published and not expired (publish_at null means "immediately").
     */
    public function scopeCurrentlyVisible(Builder $query, ?Carbon $now = null): Builder
    {
        $now ??= now();

        return $query
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('publish_at')->orWhere('publish_at', '<=', $now);
            })
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', $now);
            });
    }

    /**
     * Role-based targeting via audience JSON:
     * - null/empty => visible to everyone
     * - {"roles":["all"]} => visible to everyone
     * - {"roles":["student","teacher"]} => only those roles
     */
    public function scopeVisibleToUser(Builder $query, ?User $user): Builder
    {
        if (! $user) {
            // Guests: only show announcements intended for everyone.
            // Support both {"roles":[...]} and legacy/simple JSON arrays.
            return $query->where(function (Builder $q) {
                $q->whereNull('audience')
                    ->orWhereJsonContains('audience->roles', 'all')
                    ->orWhereJsonContains('audience', 'all');
            });
        }

        $role = $user->role;

        return $query->where(function (Builder $q) use ($role) {
            $q->whereNull('audience')
                ->orWhereJsonContains('audience->roles', 'all')
                ->orWhereJsonContains('audience->roles', $role)
                ->orWhereJsonContains('audience', 'all')
                ->orWhereJsonContains('audience', $role);
        });
    }
}
