<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'api_key',
        'is_enabled',
    ];

    // Any necessary methods can be added here if needed
}