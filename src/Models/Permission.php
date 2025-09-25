<?php

namespace Tourad\UserManager\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'group',
    ];

    // Scopes
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    // Methods
    public function getGroupedPermissions()
    {
        return static::all()->groupBy('group');
    }
}