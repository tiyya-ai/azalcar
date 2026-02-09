<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsSeo extends Model
{
    protected $table = 'ads_seo';
    
    protected $fillable = [
        'path',
        'meta_title',
        'meta_description',
        'og_image',
    ];
}
