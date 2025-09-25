<?php

namespace Tourad\UserManager\Commands;

use Illuminate\Console\Command;
use Tourad\UserManager\Facades\UserManager;

class CreateRoleCommand extends Command
{
    protected $signature = 'user-manager:create-role 
                           {name : Role name}
                           {--permissions=* : Assign permissions to role}';

    protected $description = 'Create a new role';

    public function handle()
    {
        $name = $this->argument('name');
        $permissions = $this->option('permissions');

        try {
            $role = UserManager::createRole($name, $permissions);
            
            $this->info("✅ Role '{$name}' created successfully.");
            
            if (!empty($permissions)) {
                $this->info("Permissions assigned: " . implode(', ', $permissions));
            }

        } catch (\Exception $e) {
            $this->error("❌ Failed to create role: " . $e->getMessage());
        }
    }
}