<?php

namespace Tourad\UserManager\Traits;

trait HasUserType
{
    /**
     * Check if the user has a specific user type
     */
    public function hasUserType($type): bool
    {
        if (is_string($type)) {
            return $this->userType && $this->userType->slug === $type;
        }
        
        if (is_numeric($type)) {
            return $this->user_type_id == $type;
        }
        
        return false;
    }

    /**
     * Check if the user has any of the given user types
     */
    public function hasAnyUserType(array $types): bool
    {
        foreach ($types as $type) {
            if ($this->hasUserType($type)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if the user has all of the given user types
     */
    public function hasAllUserTypes(array $types): bool
    {
        foreach ($types as $type) {
            if (!$this->hasUserType($type)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Assign a user type to the user
     */
    public function assignUserType($type): void
    {
        if (is_string($type)) {
            $userType = \Tourad\UserManager\Models\UserType::where('slug', $type)->first();
            if ($userType) {
                $this->update(['user_type_id' => $userType->id]);
            }
        } elseif (is_numeric($type)) {
            $this->update(['user_type_id' => $type]);
        }
        
        // Log the activity
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('user_type_assigned', 'User type assigned', ['type' => $type]);
        }
    }

    /**
     * Remove user type from the user
     */
    public function removeUserType(): void
    {
        $oldType = $this->user_type_id;
        $this->update(['user_type_id' => null]);
        
        // Log the activity
        if (method_exists($this, 'logActivity')) {
            $this->logActivity('user_type_removed', 'User type removed', ['old_type' => $oldType]);
        }
    }

    /**
     * Get user type permissions
     */
    public function getUserTypePermissions(): array
    {
        return $this->userType ? $this->userType->getPermissionsList() : [];
    }

    /**
     * Get user type restrictions
     */
    public function getUserTypeRestrictions(): array
    {
        return $this->userType ? $this->userType->getRestrictionsList() : [];
    }

    /**
     * Check if user type has permission
     */
    public function hasUserTypePermission(string $permission): bool
    {
        return $this->userType ? $this->userType->hasPermission($permission) : false;
    }

    /**
     * Check if user type has restriction
     */
    public function hasUserTypeRestriction(string $restriction): bool
    {
        return $this->userType ? $this->userType->hasRestriction($restriction) : false;
    }
}