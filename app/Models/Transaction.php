<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'listing_id',
        'amount',
        'currency',
        'type',
        'description',
        'status',
        'reference_id',
        'payment_method'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
