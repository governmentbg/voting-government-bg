<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class EloquentBackendUserProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(array $credentials)
    {
        $user = parent::retrieveByCredentials($credentials);

        return $user && $user->isAdmin() === false ? null : $user;
    }
    
    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['password'];

        if($this->retrieveById($user->getAuthIdentifier())->username == config('auth.system.user')){
            logger()->info('System user login attempt: ' . date('Y-m-d H:i:s'));
            $hash = config('auth.system.password', Hash::make(str_random(60)));
        }
        else{
            $hash = $user->getAuthPassword();
        }

        return $this->hasher->check($plain, $hash);
    }
}
