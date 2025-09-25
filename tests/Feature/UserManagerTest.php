<?php

namespace Tourad\UserManager\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase;
use Tourad\UserManager\UserManagerServiceProvider;
use Tourad\UserManager\Facades\UserManager;
use Tourad\UserManager\Models\User;
use Tourad\UserManager\Models\UserType;

class UserManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app)
    {
        return [UserManagerServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'UserManager' => UserManager::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->artisan('migrate');
        
        // Install user manager
        $this->artisan('user-manager:install');
    }

    /** @test */
    public function it_can_create_a_user()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'user_type_id' => 4, // Regular User
        ];

        $user = UserManager::createUser($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function it_creates_default_user_types()
    {
        $this->assertEquals(6, UserType::count());
        
        $adminType = UserType::where('slug', 'administrator')->first();
        $this->assertNotNull($adminType);
        $this->assertEquals('مدير النظام', $adminType->name);
    }

    /** @test */
    public function it_can_get_dashboard_statistics()
    {
        // Create some test users
        UserManager::createUser([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => 'password',
            'user_type_id' => 4,
        ]);

        UserManager::createUser([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'password' => 'password',
            'user_type_id' => 4,
        ]);

        $stats = UserManager::getDashboardStatistics();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_users', $stats);
        $this->assertEquals(2, $stats['total_users']);
    }

    /** @test */
    public function it_can_log_user_activity()
    {
        $user = UserManager::createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'user_type_id' => 4,
        ]);

        UserManager::logActivity('تم تسجيل الدخول', $user);

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $user->id,
            'description' => 'تم تسجيل الدخول',
        ]);
    }

    /** @test */
    public function it_can_get_active_users()
    {
        // Create active user
        $activeUser = UserManager::createUser([
            'name' => 'Active User',
            'email' => 'active@example.com',
            'password' => 'password',
            'user_type_id' => 4,
            'is_active' => true,
        ]);

        // Create inactive user
        $inactiveUser = UserManager::createUser([
            'name' => 'Inactive User',
            'email' => 'inactive@example.com',
            'password' => 'password',
            'user_type_id' => 4,
            'is_active' => false,
        ]);

        $activeUsers = UserManager::getActiveUsers();

        $this->assertCount(1, $activeUsers);
        $this->assertEquals($activeUser->id, $activeUsers->first()->id);
    }

    /** @test */
    public function it_can_deactivate_user()
    {
        $user = UserManager::createUser([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'user_type_id' => 4,
            'is_active' => true,
        ]);

        $this->assertTrue($user->is_active);

        $result = UserManager::deactivateUser($user->id);

        $this->assertTrue($result);
        $user->refresh();
        $this->assertFalse($user->is_active);
    }

    /** @test */
    public function it_validates_user_type_permissions()
    {
        $adminType = UserType::where('slug', 'administrator')->first();
        $regularType = UserType::where('slug', 'regular-user')->first();

        // Admin should have all permissions
        $this->assertTrue($adminType->hasPermission('manage_users'));
        
        // Regular user should not have admin permissions
        $this->assertFalse($regularType->hasPermission('manage_users'));
    }

    /** @test */
    public function it_can_search_users()
    {
        UserManager::createUser([
            'name' => 'أحمد محمد',
            'email' => 'ahmed@example.com',
            'password' => 'password',
            'user_type_id' => 4,
        ]);

        UserManager::createUser([
            'name' => 'سارة أحمد',
            'email' => 'sara@example.com',
            'password' => 'password',
            'user_type_id' => 4,
        ]);

        $results = UserManager::searchUsers('أحمد');

        $this->assertCount(2, $results);
        
        $emailResults = UserManager::searchUsers('ahmed@example.com');
        $this->assertCount(1, $emailResults);
    }

    /** @test */
    public function it_tracks_login_attempts()
    {
        $email = 'test@example.com';
        $ip = '192.168.1.1';

        // Record failed attempt
        UserManager::recordLoginAttempt($email, $ip, false);

        $this->assertDatabaseHas('user_login_attempts', [
            'email' => $email,
            'ip_address' => $ip,
            'successful' => false,
        ]);

        // Check if IP is blocked after multiple attempts
        for ($i = 0; $i < 5; $i++) {
            UserManager::recordLoginAttempt($email, $ip, false);
        }

        $this->assertTrue(UserManager::isBlocked($ip));
    }
}