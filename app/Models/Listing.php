<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'make_id',
        'vehicle_model_id',
        'vehicle_type_id',
        'title',
        'slug',
        'vin',
        'license_plate',
        'description',
        'price',
        'year',
        'mileage',
        'fuel_type',
        'transmission',
        'drivetrain',
        'condition',
        'color',
        'engine_size',
        'location',
        'latitude',
        'longitude',
        'is_exportable',
        'main_image',
        'images',
        'video_url',
        'v360_url',
        'media_files',
        'media_type',
        'features',
    ];

    /**
     * The attributes that should be guarded from mass assignment.
     * These are critical fields that should only be set programmatically.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
        'user_id',           // Only set on creation, never updated
        'package_id',        // Only set through package service
        'status',            // Only changed through workflow methods
        'is_featured',       // Only set through package/promotion
        'expired_at',        // Only set through package service
        'featured_until',    // Only set through promotion
        'is_reserved',       // Only set through reservation service
        'reserved_until',    // Only set through reservation service
        'uuid',              // Auto-generated
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'images' => 'array',
        'features' => 'array',
        'media_files' => 'array',
        'is_featured' => 'boolean',
        'is_exportable' => 'boolean',
        'is_reserved' => 'boolean',
        'expired_at' => 'datetime',
        'featured_until' => 'datetime',
        'reserved_until' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title . '-' . Str::random(6));
            }
            // Set default status if not set
            if (empty($model->status)) {
                $model->status = 'pending';
            }
        });

        static::updating(function ($model) {
            // Validate status transitions
            if ($model->isDirty('status')) {
                $oldStatus = $model->getOriginal('status');
                $newStatus = $model->status;
                
                $allowedTransitions = config('listing.status_flow', []);
                
                if (isset($allowedTransitions[$oldStatus])) {
                    if (!in_array($newStatus, $allowedTransitions[$oldStatus])) {
                        throw new \Exception("Invalid status transition from {$oldStatus} to {$newStatus}");
                    }
                }
            }
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function make()
    {
        return $this->belongsTo(Make::class);
    }

    public function vehicleModel()
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function isFavoritedBy($user)
    {
        if (!$user) return false;
        
        // Cache this to avoid multiple queries for the same user on the same listing instance
        if (!isset($this->is_favorited_cache)) {
            $this->is_favorited_cache = $this->favorites()->where('user_id', $user->id)->exists();
        }
        
        return $this->is_favorited_cache;
    }

    public function commission()
    {
        return $this->hasOne(Commission::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function activeReservation()
    {
        return $this->hasOne(Reservation::class)->where('status', 'active');
    }

    /**
     * Check if listing is currently reserved
     */
    public function isReserved()
    {
        return $this->is_reserved && $this->reserved_until && $this->reserved_until->isFuture();
    }

    /**
     * Get the active reservation if exists
     */
    public function getActiveReservation()
    {
        return $this->activeReservation;
    }

    /**
     * Check if listing is expired
     */
    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    /**
     * Check if listing is active and not expired
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    /**
     * Check if listing is featured and not expired
     */
    public function isFeaturedActive(): bool
    {
        return $this->is_featured && 
               $this->featured_until && 
               $this->featured_until->isFuture();
    }

    /**
     * Scope: Only active listings
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function($q) {
                        $q->whereNull('expired_at')
                          ->orWhere('expired_at', '>', now());
                    });
    }

    /**
     * Scope: Only expired listings
     */
    public function scopeExpired($query)
    {
        return $query->where('expired_at', '<=', now())
                    ->where('status', '!=', 'expired');
    }

    /**
     * Scope: Only pending approval
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Featured listings
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                    ->where(function($q) {
                        $q->whereNull('featured_until')
                          ->orWhere('featured_until', '>', now());
                    });
    }
}

