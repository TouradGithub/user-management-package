# User Management Package

حزمة إدارة المستخدمين الشاملة للارافيل مع واجهة مستخدم باللغة العربية

## Features

- ✅ **User Management**: Complete CRUD operations with soft delete
- ✅ **User Types**: Flexible user categorization system
- ✅ **Roles & Permissions**: Integration with Spatie Laravel Permission
- ✅ **Session Tracking**: Track user sessions with device information
- ✅ **Activity Logging**: Comprehensive activity tracking
- ✅ **Login Attempts**: Security features with lockout functionality
- ✅ **Multi-language Support**: Localization ready
- ✅ **Timezone Support**: User-specific timezone handling
- ✅ **Two-Factor Authentication**: Enhanced security
- ✅ **Bulk Operations**: Efficient bulk user management
- ✅ **Import/Export**: User data import and export
- ✅ **Advanced Search**: Powerful filtering and search capabilities
- ✅ **Email Verification**: Email and phone verification
- ✅ **Profile Management**: Rich user profiles with custom fields
- ✅ **Security Features**: Login attempt tracking, session management
- ✅ **Developer Friendly**: Comprehensive API with events and hooks

## Installation

### Step 1: Install the package

```bash
composer require tourad/laravel-user-manager
```

### Step 2: Publish configuration and assets

```bash
php artisan vendor:publish --provider="Tourad\UserManager\UserManagerServiceProvider"
```

Or publish specific assets:

```bash
# Publish configuration
php artisan vendor:publish --tag=user-manager-config

# Publish migrations
php artisan vendor:publish --tag=user-manager-migrations

# Publish views
php artisan vendor:publish --tag=user-manager-views

# Publish language files
php artisan vendor:publish --tag=user-manager-lang

# Publish seeders
php artisan vendor:publish --tag=user-manager-seeders

# Publish factories
php artisan vendor:publish --tag=user-manager-factories
```

### Step 3: Run migrations

```bash
php artisan migrate
```

### Step 4: Seed default data (Optional)

```bash
php artisan db:seed --class=UserManagerSeeder
```

### Step 5: Install Spatie Laravel Permission (if not already installed)

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

## Configuration

The main configuration file is `config/user-manager.php`. Here you can customize:

### Features
```php
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
```

### User Types
```php
'user_types' => [
    'enabled' => true,
    'required' => false,
    'default_type' => 'user',
    'allow_type_change' => true,
],
```

### Session Management
```php
'session' => [
    'track_sessions' => true,
    'max_concurrent_sessions' => 5,
    'session_timeout' => 120, // minutes
    'cleanup_old_sessions' => true,
    'cleanup_after_days' => 30,
],
```

### Activity Logging
```php
'activity_log' => [
    'enabled' => true,
    'log_user_activities' => true,
    'log_system_activities' => true,
    'cleanup_after_days' => 90,
    'track_ip_address' => true,
    'track_user_agent' => true,
    'track_location' => false,
],
```

## Usage

### Basic User Management

```php
use Tourad\UserManager\Facades\UserManager;

// Create a user
$user = UserManager::createUser([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'password123',
    'user_type' => 'premium', // slug or ID
    'is_active' => true,
]);

// Update user
$user = UserManager::updateUser($userId, [
    'name' => 'Jane Doe',
    'user_type' => 'administrator',
]);

// Get users with filters
$users = UserManager::getUsers([
    'search' => 'john',
    'user_type' => 'premium',
    'status' => 'active',
    'role' => 'admin',
    'date_from' => '2024-01-01',
    'date_to' => '2024-12-31',
    'sort_by' => 'created_at',
    'sort_order' => 'desc',
]);

// Get user statistics
$stats = UserManager::getUserStats();
```

### User Types Management

```php
use Tourad\UserManager\Models\UserType;

// Create user type
$userType = UserType::create([
    'name' => 'Premium User',
    'slug' => 'premium',
    'description' => 'Premium users with enhanced features',
    'permissions' => ['premium-features', 'advanced-settings'],
    'restrictions' => [],
    'icon' => 'fas fa-crown',
    'color' => '#F59E0B',
]);

// Assign user type
$user->assignUserType('premium');

// Check user type
if ($user->hasUserType('premium')) {
    // User is premium
}

// Check user type permissions
if ($user->hasUserTypePermission('premium-features')) {
    // User has premium features
}
```

### Activity Logging

```php
use Tourad\UserManager\Models\UserActivity;

// Manual activity logging
UserActivity::log('custom_action', 'User performed custom action', $user, [
    'additional_data' => 'value'
]);

// Get user activities
$activities = UserManager::getUserActivities($user, 50);

// Get system activities
$systemActivities = UserManager::getSystemActivities(100);
```

### Session Management

```php
// Get user sessions
$sessions = UserManager::getUserSessions($user);

// Terminate specific session
UserManager::terminateUserSession($user, $sessionId);

// Terminate all sessions except current
UserManager::terminateAllUserSessions($user, $currentSessionId);
```

### Bulk Operations

```php
// Bulk update users
UserManager::bulkUpdateUsers([1, 2, 3], ['is_active' => false]);

// Bulk delete users
UserManager::bulkDeleteUsers([1, 2, 3]);

// Bulk assign role
UserManager::bulkAssignRole([1, 2, 3], 'editor');
```

### Security Features

```php
// Check login attempts
$status = UserManager::checkLoginAttempts('user@example.com', '192.168.1.1');

// Log login attempt
UserManager::logLoginAttempt('user@example.com', true); // success
UserManager::logLoginAttempt('user@example.com', false, 'Invalid password'); // failure
```

## Artisan Commands

### User Management
```bash
# Create user
php artisan user-manager:create-user "John Doe" john@example.com password123

# Create role
php artisan user-manager:create-role admin "Administrator Role"

# Assign role to user
php artisan user-manager:assign-role john@example.com admin

# Create permission
php artisan user-manager:create-permission "edit-users"
```

### User Types
```bash
# Create user type
php artisan user-manager:create-user-type "Premium User" premium --description="Premium users with enhanced features" --permissions=premium-features --permissions=advanced-settings

# Assign user type
php artisan user-manager:assign-user-type john@example.com premium
```

### Maintenance
```bash
# Cleanup old sessions
php artisan user-manager:cleanup-sessions --days=30

# Cleanup inactive sessions only
php artisan user-manager:cleanup-sessions --days=7 --inactive-only
```

## Middleware

### CheckLoginAttempts
Prevents brute force attacks by limiting login attempts:

```php
// In routes/web.php or routes/api.php
Route::post('/login', [LoginController::class, 'login'])
    ->middleware('check.login.attempts');
```

### TrackUserSession
Tracks user sessions and device information:

```php
// In app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \Tourad\UserManager\Middleware\TrackUserSession::class,
    ],
];
```

### CheckUserType
Restrict access based on user types:

```php
Route::get('/premium-features', [PremiumController::class, 'index'])
    ->middleware('user.type:premium,administrator');
```

## Models

### User Model
Extended with additional features:
- User types relationship
- Session tracking
- Activity logging  
- Enhanced scopes and methods

### UserType Model
- Flexible permission system
- Restrictions management
- Meta data support
- Ordering and status

### UserActivity Model
- Comprehensive activity tracking
- Polymorphic relationships
- IP and user agent tracking
- Flexible data storage

### UserSession Model
- Device information parsing
- Session status tracking
- Location detection
- Activity timestamps

### UserLoginAttempt Model
- Failed login tracking
- IP-based lockout
- Email-based lockout
- Security analytics

## Events

The package dispatches several events:
- `UserCreated`
- `UserUpdated` 
- `UserDeleted`
- `RoleAssigned`

## Database Migrations

The package includes comprehensive migrations:
- `create_user_types_table`
- `create_users_table` (enhanced)
- `create_user_sessions_table`
- `create_user_activities_table`
- `create_user_login_attempts_table`

## Traits

### LogsActivity
Automatically logs model changes:
```php
use Tourad\UserManager\Traits\LogsActivity;

class MyModel extends Model 
{
    use LogsActivity;
}
```

### HasUserType
Adds user type functionality:
```php
use Tourad\UserManager\Traits\HasUserType;

class User extends Authenticatable 
{
    use HasUserType;
}
```

## API Endpoints

When using API routes, the package provides:
- `GET /api/user-manager/users` - List users with filters
- `POST /api/user-manager/users` - Create user
- `GET /api/user-manager/users/{id}` - Get user
- `PUT /api/user-manager/users/{id}` - Update user
- `DELETE /api/user-manager/users/{id}` - Delete user
- `GET /api/user-manager/user-types` - List user types
- `GET /api/user-manager/activities` - List activities
- `GET /api/user-manager/sessions` - List sessions

## Testing

The package includes factories for easy testing:

```php
use Tourad\UserManager\Models\User;
use Tourad\UserManager\Models\UserType;

// Create test users
$user = User::factory()->create();
$premiumUser = User::factory()->withTwoFactor()->create();

// Create test user types
$userType = UserType::factory()->active()->create();
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Support

For support, please email touradmedlemin17734@example.com or create an issue on GitHub.

## Changelog

### Version 2.0.0 (Latest)
- ✨ Added User Types system
- ✨ Added Session Management
- ✨ Added Activity Logging
- ✨ Added Login Attempts tracking
- ✨ Added Multi-language support
- ✨ Added Timezone support
- ✨ Added Enhanced security features
- ✨ Added Bulk operations
- ✨ Added Import/Export functionality
- ✨ Added Comprehensive middleware
- ✨ Added Traits for code reusability
- ✨ Added Advanced search and filtering
- 🐛 Fixed various bugs and improved performance
- 📚 Comprehensive documentation update

### Version 1.0.0
- 🎉 Initial release with basic user management
- ✨ Integration with Spatie Laravel Permission
- ✨ Soft delete functionality
- ✨ Email verification
- ✨ Basic profile management