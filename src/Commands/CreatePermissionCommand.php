<?php

namespace Tourad\UserManager\Commands;

use Illuminate\Console\Command;
use Tourad\UserManager\Facades\UserManager;

class CreatePermissionCommand extends Command
{
    protected $signature = 'user-manager:create-permission 
                           {name : Permission name}';

    protected $description = 'Create a new permission';

    public function handle()
    {
        $name = $this->argument('name');

        try {
            $permission = UserManager::createPermission($name);
            
            $this->info("✅ Permission '{$name}' created successfully.");

        } catch (\Exception $e) {
            $this->error("❌ Failed to create permission: " . $e->getMessage());
        }
    }
}