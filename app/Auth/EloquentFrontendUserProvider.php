<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class EloquentFrontendUserProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(array $credentials)
    {
        $user = parent::retrieveByCredentials($credentials);

        return $user && $user->isAdmin() === false ? $user : null;
    }   
}
