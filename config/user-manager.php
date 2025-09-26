<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Manager Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for the User Manager package.
    |
    */
    'layout' => 'layouts.admin',

    // Route configurations
    'route_prefix' => 'user-manager',
    'api_route_prefix' => 'api/user-manager',
    
    // Middleware
    'middleware' => ['web'],
    'api_middleware' => ['api', 'auth:sanctum'],

    // User Model
    'user_model' => \Tourad\UserManager\Models\User::class,

    // Features
    'features' => [
        'soft_delete' => true,
        'email_verification' => true,
        'phone_verification' => false,
        'two_factor_auth' => false,
        'social_login' => false,
        'user_registration' => true,
        'bulk_operations' => true,
        'user_impersonation' => true,
        'activity_log' => true,
        'session_management' => true,
        'user_types' => true,
        'login_attempts_tracking' => true,
        'timezone_support' => true,
        'multi_language' => true,
        'user_preferences' => true,
        'advanced_search' => true,
        'user_export_import' => true,
    ],

    // Default roles and permissions
    'default_roles' => [
        'admin' => [
            'description' => 'Full system access',
            'permissions' => ['*']
        ],
        'user' => [
            'description' => 'Basic user access',
            'permissions' => ['view-profile', 'edit-profile']
        ],
        'moderator' => [
            'description' => 'Moderate users and content',
            'permissions' => ['view-users', 'edit-users', 'view-roles']
        ],
    ],

    'default_permissions' => [
        // User permissions
        'users' => [
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'restore-users',
            'force-delete-users',
        ],
        // Role permissions
        'roles' => [
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'assign-roles',
        ],
        // Permission permissions
        'permissions' => [
            'view-permissions',
            'create-permissions',
            'edit-permissions',
            'delete-permissions',
        ],
        // Profile permissions
        'profile' => [
            'view-profile',
            'edit-profile',
            'change-password',
        ],
        // System permissions
        'system' => [
            'view-dashboard',
            'view-settings',
            'edit-settings',
            'view-logs',
        ]
    ],

    // User registration settings
    'registration' => [
        'enabled' => true,
        'email_verification_required' => true,
        'phone_verification_required' => false,
        'auto_assign_role' => 'user',
        'allowed_domains' => [], // Empty means all domains allowed
        'blocked_domains' => [],
    ],

    // User profile settings
    'profile' => [
        'required_fields' => ['name', 'email'],
        'optional_fields' => ['phone', 'avatar'],
        'custom_fields' => [
            // 'bio' => 'text',
            // 'website' => 'url',
            // 'location' => 'text',
        ],
        'avatar_disk' => 'public',
        'avatar_path' => 'avatars',
        'avatar_max_size' => 2048, // KB
    ],

    // Notification settings
    'notifications' => [
        'welcome_email' => true,
        'password_reset' => true,
        'role_assigned' => true,
        'account_deactivated' => true,
    ],

    // Security settings
    'security' => [
        'password_min_length' => 8,
        'password_requires_uppercase' => true,
        'password_requires_numbers' => true,
        'password_requires_symbols' => false,
        'login_attempts_limit' => 5,
        'login_lockout_time' => 15, // minutes
    ],

    // UI Settings
    'ui' => [
        'theme' => 'default',
        'items_per_page' => 15,
        'show_user_avatars' => true,
        'show_user_status' => true,
        'show_last_login' => true,
    ],

    // API Settings
    'api' => [
        'rate_limiting' => true,
        'rate_limit' => '60:1', // 60 requests per minute
        'pagination' => [
            'default_limit' => 15,
            'max_limit' => 100,
        ],
    ],

    // Session management
    'session' => [
        'track_sessions' => true,
        'max_concurrent_sessions' => 5,
        'session_timeout' => 120, // minutes
        'cleanup_old_sessions' => true,
        'cleanup_after_days' => 30,
    ],

    // User types
    'user_types' => [
        'enabled' => true,
        'required' => false,
        'default_type' => 'user',
        'allow_type_change' => true,
    ],

    // Activity logging
    'activity_log' => [
        'enabled' => true,
        'log_user_activities' => true,
        'log_system_activities' => true,
        'cleanup_after_days' => 90,
        'track_ip_address' => true,
        'track_user_agent' => true,
        'track_location' => false,
    ],

    // Login attempts & security
    'login_attempts' => [
        'enabled' => true,
        'max_attempts' => 5,
        'lockout_time' => 15, // minutes
        'track_by_ip' => true,
        'track_by_email' => true,
        'cleanup_after_days' => 30,
    ],

    // Localization
    'localization' => [
        'enabled' => true,
        'default_language' => 'en',
        'supported_languages' => ['en', 'ar', 'fr', 'es'],
        'default_timezone' => 'UTC',
        'auto_detect_timezone' => true,
    ],

    // File uploads
    'uploads' => [
        'avatar' => [
            'enabled' => true,
            'disk' => 'public',
            'path' => 'avatars',
            'max_size' => 2048, // KB
            'allowed_types' => ['jpg', 'jpeg', 'png', 'gif'],
            'resize' => [
                'enabled' => true,
                'width' => 300,
                'height' => 300,
            ],
        ],
    ],

    // Notifications
    'notifications' => [
        'welcome_email' => true,
        'password_reset' => true,
        'role_assigned' => true,
        'account_deactivated' => true,
        'login_from_new_device' => true,
        'password_changed' => true,
        'profile_updated' => false,
        'user_type_changed' => true,
    ],

    // Import/Export
    'import_export' => [
        'enabled' => true,
        'formats' => ['csv', 'xlsx'],
        'max_import_size' => 5000, // rows
        'validate_on_import' => true,
        'export_batch_size' => 1000,
    ],

];