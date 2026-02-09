<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'seller_id',
        'listing_id',
        'rating',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Reviewer
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id'); // Reviewed
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
