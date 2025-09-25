<?php

namespace Tourad\UserManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_info',
        'last_activity',
        'is_active',
    ];

    protected $casts = [
        'device_info' => 'array',
        'last_activity' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the session
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active sessions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive sessions
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for recent sessions
     */
    public function scopeRecent($query, $minutes = 60)
    {
        return $query->where('last_activity', '>=', now()->subMinutes($minutes));
    }

    /**
     * Get device type from user agent
     */
    public function getDeviceTypeAttribute()
    {
        $userAgent = $this->user_agent;
        
        if (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false) {
            return 'mobile';
        } elseif (strpos($userAgent, 'Tablet') !== false || strpos($userAgent, 'iPad') !== false) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    /**
     * Get browser name from user agent
     */
    public function getBrowserAttribute()
    {
        $userAgent = $this->user_agent;
        
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        }
        
        return 'Unknown';
    }

    /**
     * Terminate the session
     */
    public function terminate()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Update last activity
     */
    public function updateActivity()
    {
        $this->update(['last_activity' => now()]);
    }
}