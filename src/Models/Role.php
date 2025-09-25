<?php

namespace Tourad\UserManager\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    // Scopes
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Methods
    public function makeDefault()
    {
        // Remove default from other roles
        static::where('id', '!=', $this->id)->update(['is_default' => false]);
        
        // Set this role as default
        $this->update(['is_default' => true]);
    }

    public function getUsersCount()
    {
        return $this->users()->count();
    }
}