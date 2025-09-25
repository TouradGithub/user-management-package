<?php

namespace Tourad\UserManager\Commands;

use Illuminate\Console\Command;
use Tourad\UserManager\Facades\UserManager;

class CreateUserCommand extends Command
{
    protected $signature = 'user-manager:create-user 
                           {--name= : User name}
                           {--email= : User email}
                           {--password= : User password}
                           {--phone= : User phone}
                           {--role= : Assign role to user}
                           {--admin : Create admin user}';

    protected $description = 'Create a new user';

    public function handle()
    {
        $name = $this->option('name') ?: $this->ask('User name');
        $email = $this->option('email') ?: $this->ask('User email');
        $password = $this->option('password') ?: $this->secret('User password');
        $phone = $this->option('phone') ?: $this->ask('User phone (optional)');

        try {
            $userData = [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'phone' => $phone,
                'is_active' => true,
                'email_verified' => true,
            ];

            $user = UserManager::createUser($userData);

            // Assign role if specified
            if ($this->option('role')) {
                UserManager::assignRole($user->id, $this->option('role'));
                $this->info("Role '{$this->option('role')}' assigned to user.");
            }

            // Create admin user if specified
            if ($this->option('admin')) {
                UserManager::assignRole($user->id, 'admin');
                $this->info("Admin role assigned to user.");
            }

            $this->info("✅ User '{$name}' created successfully with ID: {$user->id}");

        } catch (\Exception $e) {
            $this->error("❌ Failed to create user: " . $e->getMessage());
        }
    }
}