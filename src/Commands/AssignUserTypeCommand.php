<?php

namespace Tourad\UserManager\Commands;

use Illuminate\Console\Command;
use Tourad\UserManager\Models\User;
use Tourad\UserManager\Models\UserType;

class AssignUserTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-manager:assign-user-type 
                            {user : The ID or email of the user}
                            {type : The ID or slug of the user type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a user type to a user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userIdentifier = $this->argument('user');
        $typeIdentifier = $this->argument('type');

        // Find user
        $user = is_numeric($userIdentifier) 
            ? User::find($userIdentifier)
            : User::where('email', $userIdentifier)->first();

        if (!$user) {
            $this->error("User not found: {$userIdentifier}");
            return 1;
        }

        // Find user type
        $userType = is_numeric($typeIdentifier)
            ? UserType::find($typeIdentifier)
            : UserType::where('slug', $typeIdentifier)->first();

        if (!$userType) {
            $this->error("User type not found: {$typeIdentifier}");
            return 1;
        }

        // Assign user type
        $user->assignUserType($userType->id);

        $this->info("User type '{$userType->name}' assigned to user '{$user->name}' successfully.");

        return 0;
    }
}