<?php

namespace Tourad\UserManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_type',
        'activity_description',
        'activity_data',
        'ip_address',
        'user_agent',
        'location',
        'subject_type',
        'subject_id',
    ];

    protected $casts = [
        'activity_data' => 'array',
    ];

    /**
     * Get the user that performed the activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject of the activity
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for specific activity type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for recent activities
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Create activity log
     */
    public static function log(
        string $type, 
        string $description, 
        $subject = null, 
        array $data = [], 
        int $userId = null
    ): self {
        return self::create([
            'user_id' => $userId ?? auth()->id(),
            'activity_type' => $type,
            'activity_description' => $description,
            'activity_data' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->getKey() : null,
        ]);
    }

    /**
     * Get formatted activity data
     */
    public function getFormattedDataAttribute()
    {
        return collect($this->activity_data)->map(function ($value, $key) {
            return ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
        })->implode(', ');
    }
}