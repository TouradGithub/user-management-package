<?php

namespace Tourad\UserManager\Commands;

use Illuminate\Console\Command;
use Tourad\UserManager\Facades\UserManager;

class AssignRoleCommand extends Command
{
    protected $signature = 'user-manager:assign-role 
                           {user : User ID or email}
                           {role : Role name}';

    protected $description = 'Assign role to user';

    public function handle()
    {
        $userIdentifier = $this->argument('user');
        $roleName = $this->argument('role');

        try {
            // Try to find user by ID first, then by email
            $user = is_numeric($userIdentifier) 
                ? \Tourad\UserManager\Models\User::findOrFail($userIdentifier)
                : \Tourad\UserManager\Models\User::where('email', $userIdentifier)->firstOrFail();

            UserManager::assignRole($user->id, $roleName);
            
            $this->info("✅ Role '{$roleName}' assigned to user '{$user->name}' successfully.");

        } catch (\Exception $e) {
            $this->error("❌ Failed to assign role: " . $e->getMessage());
        }
    }
}