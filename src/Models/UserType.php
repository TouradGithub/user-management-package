<?php

namespace Tourad\UserManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'restrictions',
        'meta_data',
        'is_active',
        'sort_order',
        'icon',
        'color',
    ];

    protected $casts = [
        'permissions' => 'array',
        'restrictions' => 'array',
        'meta_data' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get users with this type
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope for active user types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get permissions array
     */
    public function getPermissionsList(): array
    {
        return $this->permissions ?? [];
    }

    /**
     * Get restrictions array
     */
    public function getRestrictionsList(): array
    {
        return $this->restrictions ?? [];
    }

    /**
     * Check if user type has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = $this->getPermissionsList();
        return in_array($permission, $permissions) || in_array('*', $permissions);
    }

    /**
     * Check if user type has specific restriction
     */
    public function hasRestriction(string $restriction): bool
    {
        $restrictions = $this->getRestrictionsList();
        return in_array($restriction, $restrictions);
    }

    /**
     * Get meta data value
     */
    public function getMeta(string $key, $default = null)
    {
        return data_get($this->meta_data, $key, $default);
    }

    /**
     * Set meta data value
     */
    public function setMeta(string $key, $value): void
    {
        $metaData = $this->meta_data ?? [];
        data_set($metaData, $key, $value);
        $this->update(['meta_data' => $metaData]);
    }

    /**
     * Get the icon with fallback
     */
    public function getIconAttribute($value)
    {
        return $value ?? 'fas fa-user';
    }

    /**
     * Get the color with fallback
     */
    public function getColorAttribute($value)
    {
        return $value ?? '#6B7280';
    }
}