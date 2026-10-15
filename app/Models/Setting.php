<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public const CACHE_KEY = 'settings.all';

    public const UPDATED_AT = null;

    protected $fillable = [
        'key',
        'value',
        'group',
        'description',
    ];

    /** Ambil nilai setting, dengan fallback ke nilai default. */
    public static function get(string $key, $default = null)
    {
        return static::allCached()[$key] ?? $default;
    }

    public static function int(string $key, int $default = 0): int
    {
        $value = static::get($key);

        return $value === null || $value === '' ? $default : (int) $value;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = static::get($key);

        return $value === null ? $default : filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public static function set(string $key, $value, ?string $group = null, ?string $description = null): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'description' => $description]
        );

        static::flushCache();

        return $setting;
    }

    public static function group(string $group)
    {
        return static::query()->where('group', $group)->get();
    }

    /** Semua setting sebagai associative array (di-cache 5 menit). */
    public static function allCached(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addMinutes(5), fn () => static::pluck('value', 'key')->all());
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}