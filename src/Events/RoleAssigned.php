<?php

namespace Tourad\UserManager\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tourad\UserManager\Models\User;

class RoleAssigned
{
    use Dispatchable, SerializesModels;

    public $user;
    public $role;

    public function __construct(User $user, string $role)
    {
        $this->user = $user;
        $this->role = $role;
    }
}