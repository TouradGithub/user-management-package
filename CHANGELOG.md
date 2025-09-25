# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2025-09-25

### Added
- **User Types System**: Complete user categorization with permissions and restrictions
- **Session Management**: Track user sessions with device information and concurrent session limits
- **Activity Logging**: Comprehensive activity tracking for users and system events
- **Login Attempts Tracking**: Security feature to prevent brute force attacks
- **Multi-language Support**: Localization ready with language preferences
- **Timezone Support**: User-specific timezone handling
- **Enhanced User Model**: Added username, timezone, language, preferences, and enhanced features
- **Bulk Operations**: Efficient bulk user management operations
- **Import/Export Functionality**: User data import and export capabilities
- **Advanced Search**: Powerful filtering and search capabilities
- **Security Middleware**: Login attempts checking, session tracking, and user type verification
- **Traits for Code Reusability**: LogsActivity and HasUserType traits
- **Comprehensive Commands**: User type management, session cleanup, and more
- **Database Factories**: Testing support with model factories
- **Database Seeders**: Default data seeding for user types
- **Enhanced Configuration**: Extensive configuration options for all features

### Enhanced
- **User Model**: Extended with relationships, scopes, and new methods
- **UserManager Service**: Complete rewrite with all new features
- **Service Provider**: Updated with new commands and publishing options
- **Configuration File**: Comprehensive settings for all features
- **Migrations**: Enhanced user table and new tables for all features

### New Models
- `UserType`: User categorization with permissions and restrictions
- `UserSession`: Session tracking with device information
- `UserActivity`: Activity logging with polymorphic relationships
- `UserLoginAttempt`: Login attempt tracking for security

### New Commands
- `user-manager:create-user-type`: Create user types
- `user-manager:assign-user-type`: Assign user types to users
- `user-manager:cleanup-sessions`: Clean up old sessions

### New Middleware
- `CheckLoginAttempts`: Prevent brute force attacks
- `TrackUserSession`: Track user sessions and device info
- `CheckUserType`: Restrict access based on user types

### Security Features
- Login attempt tracking with lockout functionality
- IP-based and email-based lockout mechanisms
- Session management with concurrent session limits
- Device information tracking
- Enhanced activity logging

### Developer Experience
- Comprehensive documentation
- Code examples and usage guides
- Testing support with factories
- Extensive configuration options
- Event system integration

## [1.0.0] - 2024-12-01

### Added
- Initial release
- Basic user management (CRUD operations)
- Integration with Spatie Laravel Permission
- Soft delete functionality
- Email verification support
- Basic profile management
- Role and permission management
- User statistics
- Basic bulk operations
- Event system (UserCreated, UserUpdated, UserDeleted, RoleAssigned)
- Artisan commands for user, role, and permission management
- Service provider with publishable assets
- Basic configuration system

### Features
- User creation, update, and deletion
- Role-based access control
- Permission management
- User profile data management
- Settings management
- Two-factor authentication support
- Basic activity tracking
- Bulk user operations
- User impersonation support
- Email and phone verification
- User statistics and reporting