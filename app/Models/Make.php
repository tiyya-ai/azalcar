<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Make extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function models()
    {
        return $this->hasMany(VehicleModel::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}
