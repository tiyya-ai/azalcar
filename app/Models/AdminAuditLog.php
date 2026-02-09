<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAuditLog extends Model
{
    protected $fillable = [
        'admin_id',
        'action_type',
        'target_type',
        'target_id',
        'description',
        'ip_address',
        'user_agent',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Audit logs are immutable - prevent updates/deletes
     */
    public static function boot()
    {
        parent::boot();
        
        static::updating(function ($model) {
            return false; // Prevent updates
        });
        
        static::deleting(function ($model) {
            return false; // Prevent deletes
        });
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
