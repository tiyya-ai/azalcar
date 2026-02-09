<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, \Illuminate\Database\Eloquent\SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'seller_status',
        'uuid',
        'phone',
        'phone_verified_at',
        'seller_bio',
        'seller_company',
        'seller_address',
        'last_login_ip',
        'last_login_at',
        'status',
        'ban_reason'
    ];

    /**
     * The attributes that should be guarded from mass assignment.
     *
     * @var list<string>
     */
    protected $guarded = [
        'role',
        'balance',
        'seller_approved_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'seller_approved_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
    
    public function getIsAdminAttribute()
    {
        return $this->role === 'admin';
    }

    public function getIsSellerAttribute()
    {
        return $this->role === 'vendor' || $this->seller_status === 'approved';
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Safely update user balance with validation
     */
    public function updateBalance(float $amount, string $operation = 'add'): bool
    {
        try {
            return DB::transaction(function () use ($amount, $operation) {
                $user = static::where('id', $this->id)->lockForUpdate()->first();
                if (!$user) return false;

                $current = (float) $user->balance;
                $newBalance = $operation === 'subtract' ? $current - $amount : $current + $amount;

                if ($newBalance < 0) {
                    Log::warning('Attempted negative balance update', [
                        'user_id' => $this->id,
                        'current_balance' => $current,
                        'amount' => $amount,
                        'operation' => $operation,
                        'new_balance' => $newBalance
                    ]);
                    return false;
                }

                if ($newBalance > 1000000) {
                    Log::warning('Balance exceeds maximum limit', [
                        'user_id' => $this->id,
                        'attempted_balance' => $newBalance
                    ]);
                    return false;
                }

                $user->balance = $newBalance;
                return $user->save();
            });
        } catch (\Exception $e) {
            Log::error('Exception updating balance', ['error' => $e->getMessage(), 'user_id' => $this->id]);
            return false;
        }
    }
    
    public function scopeSellers($query)
    {
        return $query->where('role', 'vendor')->orWhere('seller_status', 'approved');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'seller_id');
    }

    public function getAverageRatingAttribute()
    {
        // Simple caching or direct query. For now direct.
        // Eager load this if listing many users.
        return round($this->reviewsReceived()->avg('rating') ?? 0, 1);
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviewsReceived()->count();
    }
}
