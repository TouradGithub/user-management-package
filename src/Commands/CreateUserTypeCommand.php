<?php

namespace Tourad\UserManager\Commands;

use Illuminate\Console\Command;
use Tourad\UserManager\Models\UserType;

class CreateUserTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-manager:create-user-type 
                            {name : The name of the user type}
                            {slug : The slug of the user type}
                            {--description= : Description of the user type}
                            {--permissions=* : Permissions for the user type}
                            {--restrictions=* : Restrictions for the user type}
                            {--icon= : Icon for the user type}
                            {--color= : Color for the user type}
                            {--inactive : Create as inactive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user type';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $slug = $this->argument('slug');

        // Check if user type already exists
        if (UserType::where('slug', $slug)->exists()) {
            $this->error("User type with slug '{$slug}' already exists.");
            return 1;
        }

        $userType = UserType::create([
            'name' => $name,
            'slug' => $slug,
            'description' => $this->option('description'),
            'permissions' => $this->option('permissions'),
            'restrictions' => $this->option('restrictions'),
            'icon' => $this->option('icon') ?? 'fas fa-user',
            'color' => $this->option('color') ?? '#6B7280',
            'is_active' => !$this->option('inactive'),
        ]);

        $this->info("User type '{$name}' created successfully with ID: {$userType->id}");

        return 0;
    }
}