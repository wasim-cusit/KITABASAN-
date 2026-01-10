<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'code',
        'description',
        'icon',
        'credentials',
        'config',
        'is_active',
        'is_sandbox',
        'order',
        'transaction_fee_percentage',
        'transaction_fee_fixed',
        'instructions',
    ];

    protected $casts = [
        'credentials' => 'array',
        'config' => 'array',
        'is_active' => 'boolean',
        'is_sandbox' => 'boolean',
        'transaction_fee_percentage' => 'decimal:2',
        'transaction_fee_fixed' => 'decimal:2',
        'order' => 'integer',
    ];

    /**
     * Get active payment methods
     */
    public static function getActive()
    {
        return self::where('is_active', true)->orderBy('order')->get();
    }

    /**
     * Calculate transaction fee for an amount
     */
    public function calculateFee($amount): float
    {
        $percentageFee = ($amount * $this->transaction_fee_percentage) / 100;
        return $percentageFee + $this->transaction_fee_fixed;
    }

    /**
     * Get total amount with fees
     */
    public function getTotalWithFees($amount): float
    {
        return $amount + $this->calculateFee($amount);
    }

    /**
     * Get credential value
     */
    public function getCredential(string $key, $default = null)
    {
        return $this->credentials[$key] ?? $default;
    }

    /**
     * Get config value
     */
    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }
}
