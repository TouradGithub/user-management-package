<?php

namespace Tourad\UserManager\Facades;

use Illuminate\Support\Facades\Facade;

class UserManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'user-manager';
    }
}