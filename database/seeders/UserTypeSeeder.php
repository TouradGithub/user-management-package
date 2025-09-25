<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Tourad\UserManager\Models\UserType;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userTypes = [
            [
                'name' => 'Administrator',
                'slug' => 'administrator',
                'description' => 'Full system administrator with all permissions',
                'permissions' => ['*'],
                'restrictions' => [],
                'meta_data' => [
                    'max_storage' => null,
                    'max_uploads' => null,
                    'features' => ['dashboard', 'users', 'roles', 'permissions', 'settings'],
                ],
                'is_active' => true,
                'sort_order' => 1,
                'icon' => 'fas fa-user-shield',
                'color' => '#DC2626',
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manager with limited administrative permissions',
                'permissions' => [
                    'view-users', 'create-users', 'edit-users',
                    'view-roles', 'assign-roles',
                    'view-dashboard', 'view-reports'
                ],
                'restrictions' => ['delete-users', 'delete-roles'],
                'meta_data' => [
                    'max_storage' => '10GB',
                    'max_uploads' => 1000,
                    'features' => ['dashboard', 'users', 'reports'],
                ],
                'is_active' => true,
                'sort_order' => 2,
                'icon' => 'fas fa-user-tie',
                'color' => '#2563EB',
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Staff member with basic operational permissions',
                'permissions' => [
                    'view-profile', 'edit-profile',
                    'view-dashboard', 'view-reports'
                ],
                'restrictions' => ['view-users', 'edit-users'],
                'meta_data' => [
                    'max_storage' => '5GB',
                    'max_uploads' => 500,
                    'features' => ['dashboard', 'profile'],
                ],
                'is_active' => true,
                'sort_order' => 3,
                'icon' => 'fas fa-user-cog',
                'color' => '#059669',
            ],
            [
                'name' => 'Regular User',
                'slug' => 'user',
                'description' => 'Regular user with basic access permissions',
                'permissions' => [
                    'view-profile', 'edit-profile', 'change-password'
                ],
                'restrictions' => ['view-dashboard', 'view-users'],
                'meta_data' => [
                    'max_storage' => '1GB',
                    'max_uploads' => 100,
                    'features' => ['profile'],
                ],
                'is_active' => true,
                'sort_order' => 4,
                'icon' => 'fas fa-user',
                'color' => '#6B7280',
            ],
            [
                'name' => 'Guest',
                'slug' => 'guest',
                'description' => 'Guest user with very limited permissions',
                'permissions' => [
                    'view-profile'
                ],
                'restrictions' => ['edit-profile', 'change-password'],
                'meta_data' => [
                    'max_storage' => '100MB',
                    'max_uploads' => 10,
                    'features' => [],
                ],
                'is_active' => true,
                'sort_order' => 5,
                'icon' => 'fas fa-user-clock',
                'color' => '#9CA3AF',
            ],
            [
                'name' => 'Premium User',
                'slug' => 'premium',
                'description' => 'Premium user with enhanced features',
                'permissions' => [
                    'view-profile', 'edit-profile', 'change-password',
                    'premium-features', 'advanced-settings'
                ],
                'restrictions' => [],
                'meta_data' => [
                    'max_storage' => '50GB',
                    'max_uploads' => 5000,
                    'features' => ['profile', 'premium-dashboard', 'analytics'],
                    'subscription_required' => true,
                ],
                'is_active' => true,
                'sort_order' => 6,
                'icon' => 'fas fa-crown',
                'color' => '#F59E0B',
            ],
        ];

        foreach ($userTypes as $userType) {
            UserType::updateOrCreate(
                ['slug' => $userType['slug']], 
                $userType
            );
        }
    }
}