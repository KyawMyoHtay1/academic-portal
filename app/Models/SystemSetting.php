<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'updated_by',
    ];

    /**
     * Safely fetch a setting value. Falls back when table is unavailable.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        if (! self::tableExists()) {
            return $default;
        }

        try {
            $value = self::query()
                ->where('key', $key)
                ->value('value');
        } catch (QueryException) {
            return $default;
        }

        return $value ?? $default;
    }

    /**
     * Safely persist a key/value setting.
     */
    public static function setValue(string $key, string|int|float|bool|null $value, ?int $updatedBy = null): void
    {
        if (! self::tableExists()) {
            return;
        }

        try {
            self::query()->updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value === null ? null : (string) $value,
                    'updated_by' => $updatedBy,
                ]
            );
        } catch (QueryException) {
            // Ignore missing-table/race startup cases and keep request flow alive.
        }
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    private static function tableExists(): bool
    {
        static $exists = null;

        if ($exists !== null) {
            return $exists;
        }

        try {
            $exists = Schema::hasTable('system_settings');
        } catch (\Throwable) {
            $exists = false;
        }

        return $exists;
    }
}
