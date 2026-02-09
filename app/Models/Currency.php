<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate',
        'is_active'
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'is_active' => 'boolean'
    ];

    /**
     * Scope for active currencies
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Convert amount from base currency to this currency
     */
    public function convertFromBase($amount)
    {
        return $amount * $this->exchange_rate;
    }

    /**
     * Convert amount from this currency to base currency
     */
    public function convertToBase($amount)
    {
        return $amount / $this->exchange_rate;
    }
}
