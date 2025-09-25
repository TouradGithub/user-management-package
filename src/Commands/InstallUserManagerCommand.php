<?php

namespace Tourad\UserManager\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallUserManagerCommand extends Command
{
    protected $signature = 'user-manager:install 
                           {--force : Force the operation to run when in production}
                           {--seed : Run seeders after installation}';

    protected $description = 'Install the User Manager package';

    public function handle()
    {
        $this->info('Installing User Manager Package...');

        // Publish config
        $this->call('vendor:publish', [
            '--tag' => 'user-manager-config',
            '--force' => $this->option('force')
        ]);

        // Publish migrations
        $this->call('vendor:publish', [
            '--tag' => 'user-manager-migrations',
            '--force' => $this->option('force')
        ]);

        // Publish views
        $this->call('vendor:publish', [
            '--tag' => 'user-manager-views',
            '--force' => $this->option('force')
        ]);

        // Publish translations
        $this->call('vendor:publish', [
            '--tag' => 'user-manager-lang',
            '--force' => $this->option('force')
        ]);

        // Install Spatie Permission if not already installed
        if (!class_exists('Spatie\Permission\PermissionServiceProvider')) {
            $this->info('Installing Spatie Laravel Permission...');
            $this->call('vendor:publish', [
                '--provider' => 'Spatie\Permission\PermissionServiceProvider'
            ]);
        }

        // Run migrations
        $this->info('Running migrations...');
        $this->call('migrate');

        // Run seeders if requested
        if ($this->option('seed')) {
            $this->info('Running seeders...');
            $this->call('db:seed', ['--class' => 'UserManagerSeeder']);
        }

        $this->info('✅ User Manager Package installed successfully!');
        $this->line('');
        $this->line('Next steps:');
        $this->line('1. Configure your settings in config/user-manager.php');
        $this->line('2. Create your first admin user: php artisan user-manager:create-user');
        $this->line('3. Create roles: php artisan user-manager:create-role');
        $this->line('4. Visit /user-manager to access the dashboard');
    }
}