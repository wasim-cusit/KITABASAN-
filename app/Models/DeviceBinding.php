<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceBinding extends Model
{
    protected $fillable = [
        'user_id',
        'device_fingerprint',
        'device_name',
        'ip_address',
        'user_agent',
        'status',
        'last_used_at',
        'reset_requested_at',
        'reset_request_reason',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'reset_requested_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
