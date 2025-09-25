<?php

namespace Tourad\UserManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'successful',
        'user_agent',
        'failure_reason',
    ];

    protected $casts = [
        'successful' => 'boolean',
    ];

    /**
     * Scope for successful attempts
     */
    public function scopeSuccessful($query)
    {
        return $query->where('successful', true);
    }

    /**
     * Scope for failed attempts
     */
    public function scopeFailed($query)
    {
        return $query->where('successful', false);
    }

    /**
     * Scope for specific email
     */
    public function scopeForEmail($query, string $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Scope for specific IP
     */
    public function scopeForIp($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Scope for recent attempts
     */
    public function scopeRecent($query, int $minutes = 15)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Log login attempt
     */
    public static function logAttempt(string $email, bool $successful = false, string $failureReason = null): self
    {
        return self::create([
            'email' => $email,
            'ip_address' => request()->ip(),
            'successful' => $successful,
            'user_agent' => request()->userAgent(),
            'failure_reason' => $failureReason,
        ]);
    }

    /**
     * Check if IP is currently locked out
     */
    public static function isIpLockedOut(string $ip, int $maxAttempts = 5, int $lockoutMinutes = 15): bool
    {
        $recentFailedAttempts = self::forIp($ip)
            ->failed()
            ->recent($lockoutMinutes)
            ->count();

        return $recentFailedAttempts >= $maxAttempts;
    }

    /**
     * Check if email is currently locked out
     */
    public static function isEmailLockedOut(string $email, int $maxAttempts = 5, int $lockoutMinutes = 15): bool
    {
        $recentFailedAttempts = self::forEmail($email)
            ->failed()
            ->recent($lockoutMinutes)
            ->count();

        return $recentFailedAttempts >= $maxAttempts;
    }
}