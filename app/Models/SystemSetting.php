<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'name',
        'value',
        'type',
        'group',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get a system setting value by key
     */
    public static function getValue(string $key, $default = null)
    {
        return Cache::remember("system_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->where('is_active', true)->first();
            return $setting ? self::castValue($setting->value, $setting->type) : $default;
        });
    }

    /**
     * Set a system setting value
     */
    public static function setValue(string $key, $value): bool
    {
        $setting = self::where('key', $key)->first();
        if ($setting) {
            // Store the value as-is (null, empty string, or string value)
            // The database column is nullable, so we can store null
            $setting->update(['value' => $value]);
            Cache::forget("system_setting_{$key}");

            return true;
        }
        return false;
    }

    /**
     * Get all settings grouped by group
     */
    public static function getGrouped(): array
    {
        return Cache::remember('system_settings_grouped', 3600, function () {
            $settings = self::where('is_active', true)
                ->orderBy('group')
                ->orderBy('order')
                ->get();

            $grouped = [];

            foreach ($settings as $setting) {
                $group = $setting->group ?? 'general';
                if (!isset($grouped[$group])) {
                    $grouped[$group] = [];
                }

                $grouped[$group][] = [
                    'id' => $setting->id,
                    'key' => $setting->key,
                    'name' => $setting->name,
                    'value' => $setting->value,
                    'type' => $setting->type,
                    'group' => $setting->group,
                    'description' => $setting->description,
                    'order' => $setting->order,
                    'is_active' => $setting->is_active,
                ];
            }

            return $grouped;
        });
    }

    /**
     * Cast value based on type
     */
    protected static function castValue($value, string $type)
    {
        if ($value === null) {
            return null;
        }

        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
            case 'integer':
                return is_numeric($value) ? (int) $value : $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Clear all system settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget('system_settings_grouped');
        $keys = self::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("system_setting_{$key}");
        }
    }

    protected static function booted(): void
    {
        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }
}
