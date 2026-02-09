<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'duration_days',
        'is_featured',
        'is_top',
        'limit_images',
        'max_listings',
        'max_featured_days',
        'is_active',
        'sort_order',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'is_featured' => 'boolean',
        'is_top' => 'boolean',
        'limit_images' => 'integer',
        'max_listings' => 'integer',
        'max_featured_days' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get listings associated with this package
     */
    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    /**
     * Scope: Only active packages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    /**
     * Check if user can create a listing with this package
     */
    public function canCreateListing(\App\Models\User $user): bool
    {
        // Check if user has reached max listings for this package
        $activeListings = $user->listings()
            ->where('package_id', $this->id)
            ->whereIn('status', ['active', 'pending', 'approved'])
            ->count();

        return $activeListings < $this->max_listings;
    }

    /**
     * Check if this package allows featuring
     */
    public function canFeatureListing(): bool
    {
        return $this->is_featured && $this->max_featured_days > 0;
    }

    /**
     * Validate image count against package limit
     */
    public function validateImageCount(int $count): bool
    {
        return $count <= $this->limit_images;
    }

    /**
     * Get the expiration date for a listing created with this package
     */
    public function getExpirationDate(): \Carbon\Carbon
    {
        return now()->addDays($this->duration_days);
    }

    /**
     * Get the featured expiration date if applicable
     */
    public function getFeaturedExpirationDate(): ?\Carbon\Carbon
    {
        if (!$this->canFeatureListing()) {
            return null;
        }

        return now()->addDays($this->max_featured_days);
    }
}
