<?php

namespace Tourad\UserManager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Tourad\UserManager\Commands\InstallUserManagerCommand;
use Tourad\UserManager\Commands\CreateUserCommand;
use Tourad\UserManager\Commands\CreateRoleCommand;
use Tourad\UserManager\Commands\AssignRoleCommand;
use Tourad\UserManager\Commands\CreatePermissionCommand;
use Tourad\UserManager\Commands\CreateUserTypeCommand;
use Tourad\UserManager\Commands\AssignUserTypeCommand;
use Tourad\UserManager\Commands\CleanupSessionsCommand;

class UserManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/user-manager.php', 'user-manager');

        $this->app->singleton('user-manager', function () {
            return new UserManagerService();
        });
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'user-manager');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'user-manager');

        // Register routes
        $this->registerRoutes();

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallUserManagerCommand::class,
                CreateUserCommand::class,
                CreateRoleCommand::class,
                AssignRoleCommand::class,
                CreatePermissionCommand::class,
                CreateUserTypeCommand::class,
                AssignUserTypeCommand::class,
                CleanupSessionsCommand::class,
            ]);

            // Publish assets
            $this->publishes([
                __DIR__ . '/../config/user-manager.php' => config_path('user-manager.php'),
            ], 'user-manager-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/user-manager'),
            ], 'user-manager-views');

            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/user-manager'),
            ], 'user-manager-lang');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'user-manager-migrations');

            $this->publishes([
                __DIR__ . '/../database/seeders' => database_path('seeders'),
            ], 'user-manager-seeders');

            $this->publishes([
                __DIR__ . '/../database/factories' => database_path('factories'),
            ], 'user-manager-factories');
        }
    }

    protected function registerRoutes()
    {
        Route::group([
            'prefix' => config('user-manager.route_prefix', 'user-manager'),
            'middleware' => config('user-manager.middleware', ['web']),
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }
}