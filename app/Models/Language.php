<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'name',
        'code',
        'native_name',
        'flag',
        'is_active',
        'is_default',
        'order',
        'direction',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get active languages
     */
    public static function getActive()
    {
        return self::where('is_active', true)->orderBy('order')->get();
    }

    /**
     * Get default language
     */
    public static function getDefault()
    {
        return self::where('is_default', true)->where('is_active', true)->first();
    }

    /**
     * Set as default language
     */
    public function setAsDefault(): void
    {
        // Remove default from all languages
        self::where('is_default', true)->update(['is_default' => false]);
        
        // Set this as default
        $this->update(['is_default' => true]);
    }
}
