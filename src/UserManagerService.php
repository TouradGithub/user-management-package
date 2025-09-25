<?php

namespace Tourad\UserManager;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tourad\UserManager\Models\User;
use Tourad\UserManager\Models\UserType;
use Tourad\UserManager\Models\UserActivity;
use Tourad\UserManager\Models\UserSession;
use Tourad\UserManager\Models\UserLoginAttempt;
use Tourad\UserManager\Events\UserCreated;
use Tourad\UserManager\Events\UserUpdated;
use Tourad\UserManager\Events\UserDeleted;
use Tourad\UserManager\Events\RoleAssigned;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserManagerService
{
    /**
     * Create a new user
     */
    public function createUser(array $data): User
    {
        // Handle user type
        if (isset($data['user_type']) && is_string($data['user_type'])) {
            $userType = UserType::where('slug', $data['user_type'])->first();
            $data['user_type_id'] = $userType?->id;
            unset($data['user_type']);
        }

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = User::create($data);

        // Auto-assign default role if configured
        if (config('user-manager.registration.auto_assign_role')) {
            $defaultRole = config('user-manager.registration.auto_assign_role');
            $role = Role::where('name', $defaultRole)->first();
            if ($role) {
                $user->assignRole($role);
            }
        }

        // Log activity
        UserActivity::log('user_created', 'New user account created', $user);

        event(new UserCreated($user));

        return $user;
    }

    /**
     * Update user
     */
    public function updateUser($userId, array $data): User
    {
        $user = User::findOrFail($userId);

        // Handle user type
        if (isset($data['user_type']) && is_string($data['user_type'])) {
            $userType = UserType::where('slug', $data['user_type'])->first();
            $data['user_type_id'] = $userType?->id;
            unset($data['user_type']);
        }

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        event(new UserUpdated($user));

        return $user;
    }

    /**
     * Delete user (soft delete)
     */
    public function deleteUser($userId, bool $forceDelete = false): bool
    {
        $user = User::findOrFail($userId);

        UserActivity::log(
            $forceDelete ? 'user_force_deleted' : 'user_deleted', 
            $forceDelete ? 'User account permanently deleted' : 'User account deleted', 
            $user
        );

        if ($forceDelete) {
            // Clean up related data
            $user->sessions()->delete();
            $user->activities()->delete();
            $user->forceDelete();
        } else {
            $user->delete();
        }

        event(new UserDeleted($user, $forceDelete));

        return true;
    }

    /**
     * Restore deleted user
     */
    public function restoreUser($userId): User
    {
        $user = User::withTrashed()->findOrFail($userId);
        $user->restore();

        UserActivity::log('user_restored', 'User account restored', $user);

        return $user;
    }

    /**
     * Create role
     */
    public function createRole(string $name, array $permissions = []): Role
    {
        $role = Role::create(['name' => $name]);
        
        if (!empty($permissions)) {
            $role->givePermissionTo($permissions);
        }

        return $role;
    }

    /**
     * Create permission
     */
    public function createPermission(string $name): Permission
    {
        return Permission::create(['name' => $name]);
    }

    /**
     * Assign role to user
     */
    public function assignRole($userId, $roleName): User
    {
        $user = User::findOrFail($userId);
        $user->assignRole($roleName);

        UserActivity::log('role_assigned', "Role '{$roleName}' assigned to user", $user, ['role' => $roleName]);

        event(new RoleAssigned($user, $roleName));

        return $user;
    }

    /**
     * Remove role from user
     */
    public function removeRole($userId, $roleName): User
    {
        $user = User::findOrFail($userId);
        $user->removeRole($roleName);

        UserActivity::log('role_removed', "Role '{$roleName}' removed from user", $user, ['role' => $roleName]);

        return $user;
    }

    /**
     * Get users with filters
     */
    public function getUsers(array $filters = []): LengthAwarePaginator
    {
        $query = User::query()->with(['userType', 'roles']);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['user_type'])) {
            if (is_array($filters['user_type'])) {
                $query->whereIn('user_type_id', $filters['user_type']);
            } else {
                $query->where('user_type_id', $filters['user_type']);
            }
        }

        if (!empty($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        if (isset($filters['active'])) {
            $query->where('is_active', $filters['active']);
        }

        if (!empty($filters['status'])) {
            switch ($filters['status']) {
                case 'active':
                    $query->active();
                    break;
                case 'inactive':
                    $query->inactive();
                    break;
                case 'verified':
                    $query->verified();
                    break;
                case 'unverified':
                    $query->unverified();
                    break;
            }
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get user statistics
     */
    public function getUserStats(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::active()->count(),
            'inactive_users' => User::inactive()->count(),
            'verified_users' => User::verified()->count(),
            'unverified_users' => User::unverified()->count(),
            'deleted_users' => User::onlyTrashed()->count(),
            'users_today' => User::whereDate('created_at', today())->count(),
            'users_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'users_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'recent_logins' => User::recentlyActive(7)->count(),
            'user_types' => UserType::active()->withCount('users')->get()->pluck('users_count', 'name')->toArray(),
            'users_by_role' => DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->selectRaw('roles.name, COUNT(*) as count')
                ->groupBy('roles.name')
                ->get()
                ->pluck('count', 'name')
                ->toArray(),
        ];
    }

    /**
     * Bulk operations
     */
    public function bulkUpdateUsers(array $userIds, array $data): int
    {
        $updated = User::whereIn('id', $userIds)->update($data);
        
        // Log bulk activity
        UserActivity::log('bulk_user_update', "Bulk updated {$updated} users", null, [
            'user_ids' => $userIds,
            'data' => $data,
            'count' => $updated,
        ]);
        
        return $updated;
    }

    public function bulkDeleteUsers(array $userIds): int
    {
        $deleted = User::whereIn('id', $userIds)->delete();
        
        UserActivity::log('bulk_user_delete', "Bulk deleted {$deleted} users", null, [
            'user_ids' => $userIds,
            'count' => $deleted,
        ]);
        
        return $deleted;
    }

    public function bulkAssignRole(array $userIds, string $roleName): int
    {
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            return 0;
        }

        $users = User::whereIn('id', $userIds)->get();
        $assigned = 0;

        foreach ($users as $user) {
            if (!$user->hasRole($roleName)) {
                $user->assignRole($role);
                $assigned++;
            }
        }

        UserActivity::log('bulk_role_assign', "Bulk assigned role '{$roleName}' to {$assigned} users", null, [
            'user_ids' => $userIds,
            'role' => $roleName,
            'count' => $assigned,
        ]);

        return $assigned;
    }

    /**
     * User type management
     */
    public function createUserType(array $data): UserType
    {
        return UserType::create($data);
    }

    public function getUserTypes(bool $activeOnly = true): Collection
    {
        $query = UserType::query();
        
        if ($activeOnly) {
            $query->active();
        }
        
        return $query->ordered()->get();
    }

    /**
     * Session management
     */
    public function getUserSessions(User $user): Collection
    {
        return $user->sessions()->active()->orderBy('last_activity', 'desc')->get();
    }

    public function terminateUserSession(User $user, string $sessionId): bool
    {
        $session = $user->sessions()->where('session_id', $sessionId)->first();
        
        if ($session) {
            $session->terminate();
            UserActivity::log('session_terminated', 'User session terminated', $user, ['session_id' => $sessionId]);
            return true;
        }
        
        return false;
    }

    public function terminateAllUserSessions(User $user, string $exceptSessionId = null): int
    {
        $query = $user->sessions()->active();
        
        if ($exceptSessionId) {
            $query->where('session_id', '!=', $exceptSessionId);
        }
        
        $count = $query->count();
        $query->update(['is_active' => false]);
        
        UserActivity::log('all_sessions_terminated', "Terminated {$count} user sessions", $user, [
            'count' => $count,
            'except_session' => $exceptSessionId,
        ]);
        
        return $count;
    }

    /**
     * Activity tracking
     */
    public function getUserActivities(User $user, int $limit = 50): Collection
    {
        return $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getSystemActivities(int $limit = 100): Collection
    {
        return UserActivity::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Security features
     */
    public function checkLoginAttempts(string $email, string $ip): array
    {
        $emailLocked = UserLoginAttempt::isEmailLockedOut($email);
        $ipLocked = UserLoginAttempt::isIpLockedOut($ip);
        
        return [
            'email_locked' => $emailLocked,
            'ip_locked' => $ipLocked,
            'can_login' => !$emailLocked && !$ipLocked,
        ];
    }

    public function logLoginAttempt(string $email, bool $successful = false, string $failureReason = null): UserLoginAttempt
    {
        return UserLoginAttempt::logAttempt($email, $successful, $failureReason);
    }

    /**
     * Export users
     */
    public function exportUsers(array $filters = []): Collection
    {
        $query = User::query()->with(['userType', 'roles']);
        
        // Apply same filters as getUsers method but without pagination
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['user_type'])) {
            $query->where('user_type_id', $filters['user_type']);
        }

        if (!empty($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }
        
        return $query->get();
    }

    /**
     * Import users from array
     */
    public function importUsers(array $usersData): array
    {
        $imported = [];
        $errors = [];
        
        foreach ($usersData as $index => $userData) {
            try {
                $user = $this->createUser($userData);
                $imported[] = $user;
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'data' => $userData,
                    'error' => $e->getMessage(),
                ];
            }
        }
        
        UserActivity::log('bulk_user_import', "Imported " . count($imported) . " users", null, [
            'success_count' => count($imported),
            'error_count' => count($errors),
        ]);
        
        return [
            'imported' => $imported,
            'errors' => $errors,
            'success_count' => count($imported),
            'error_count' => count($errors),
        ];
    }
}