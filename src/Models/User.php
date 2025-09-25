<?php

namespace Tourad\UserManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tourad\UserManager\Traits\LogsActivity;
use Tourad\UserManager\Traits\HasUserType;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles, LogsActivity, HasUserType;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'phone',
        'avatar',
        'is_active',
        'email_verified_at',
        'phone_verified_at',
        'last_login_at',
        'last_login_ip',
        'timezone',
        'language',
        'user_type_id',
        'profile_data',
        'settings',
        'preferences',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'two_factor_confirmed_at' => 'boolean',
        'profile_data' => 'array',
        'settings' => 'array',
        'preferences' => 'array',
        'two_factor_recovery_codes' => 'array',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get the user type that this user belongs to
     */
    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    /**
     * Get user's sessions
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    /**
     * Get user's activities
     */
    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    /**
     * Get user's login attempts
     */
    public function loginAttempts(): HasMany
    {
        return $this->hasMany(UserLoginAttempt::class, 'email', 'email');
    }

    /**
     * Get all of the user's activity logs
     */
    public function activityLogs(): MorphMany
    {
        return $this->morphMany(UserActivity::class, 'subject');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    public function scopeOfType($query, $typeId)
    {
        return $query->where('user_type_id', $typeId);
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    public function scopeByTimezone($query, $timezone)
    {
        return $query->where('timezone', $timezone);
    }

    public function scopeRecentlyActive($query, $days = 30)
    {
        return $query->where('last_login_at', '>=', now()->subDays($days));
    }

    // Mutators
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function getIsVerifiedAttribute()
    {
        return !is_null($this->email_verified_at);
    }

    // Methods
    public function markEmailAsVerified()
    {
        $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();

        return $this;
    }

    public function markPhoneAsVerified()
    {
        $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();

        return $this;
    }

    public function updateLastLogin()
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);

        // Log the activity
        UserActivity::log('login', 'User logged in', $this);
    }

    public function activate()
    {
        $this->update(['is_active' => true]);
        UserActivity::log('user_activated', 'User account activated', $this);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
        UserActivity::log('user_deactivated', 'User account deactivated', $this);
    }

    public function getProfileData($key = null, $default = null)
    {
        if ($key) {
            return data_get($this->profile_data, $key, $default);
        }
        
        return $this->profile_data;
    }

    public function setProfileData($key, $value = null)
    {
        $profileData = $this->profile_data ?? [];
        
        if (is_array($key)) {
            $profileData = array_merge($profileData, $key);
        } else {
            data_set($profileData, $key, $value);
        }
        
        $this->update(['profile_data' => $profileData]);
        UserActivity::log('profile_updated', 'User profile data updated', $this);
    }

    public function getSetting($key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    public function setSetting($key, $value = null)
    {
        $settings = $this->settings ?? [];
        
        if (is_array($key)) {
            $settings = array_merge($settings, $key);
        } else {
            data_set($settings, $key, $value);
        }
        
        $this->update(['settings' => $settings]);
    }

    public function getPreference($key, $default = null)
    {
        return data_get($this->preferences, $key, $default);
    }

    public function setPreference($key, $value = null)
    {
        $preferences = $this->preferences ?? [];
        
        if (is_array($key)) {
            $preferences = array_merge($preferences, $key);
        } else {
            data_set($preferences, $key, $value);
        }
        
        $this->update(['preferences' => $preferences]);
    }

    /**
     * Get user's active sessions
     */
    public function getActiveSessionsAttribute()
    {
        return $this->sessions()->active()->get();
    }

    /**
     * Get user's recent activities
     */
    public function getRecentActivitiesAttribute()
    {
        return $this->activities()->recent()->orderBy('created_at', 'desc')->take(10)->get();
    }

    /**
     * Check if user has specific user type
     */
    public function hasUserType(string $typeName): bool
    {
        return $this->userType && $this->userType->slug === $typeName;
    }

    /**
     * Check if user type has permission
     */
    public function hasUserTypePermission(string $permission): bool
    {
        return $this->userType && $this->userType->hasPermission($permission);
    }

    /**
     * Terminate all user sessions except current
     */
    public function terminateOtherSessions(string $currentSessionId = null)
    {
        $query = $this->sessions()->active();
        
        if ($currentSessionId) {
            $query->where('session_id', '!=', $currentSessionId);
        }
        
        $query->update(['is_active' => false]);
        
        UserActivity::log('sessions_terminated', 'Other sessions terminated', $this);
    }

    /**
     * Get user's display name based on preferences
     */
    public function getDisplayNameAttribute()
    {
        $preference = $this->getPreference('display_name_format', 'name');
        
        switch ($preference) {
            case 'username':
                return $this->username ?: $this->name;
            case 'email':
                return $this->email;
            default:
                return $this->name;
        }
    }

    /**
     * Get user's timezone aware timestamp
     */
    public function getLocalTime($timestamp = null)
    {
        $timestamp = $timestamp ?: now();
        return $timestamp->setTimezone($this->timezone);
    }
}