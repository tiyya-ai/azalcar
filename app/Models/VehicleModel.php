<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Make;
use App\Models\Listing;

class VehicleModel extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function make()
    {
        return $this->belongsTo(Make::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}
