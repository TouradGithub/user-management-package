<?php

namespace Tourad\UserManager\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tourad\UserManager\Models\User;

class UserDeleted
{
    use Dispatchable, SerializesModels;

    public $user;
    public $forceDeleted;

    public function __construct(User $user, bool $forceDeleted = false)
    {
        $this->user = $user;
        $this->forceDeleted = $forceDeleted;
    }
}