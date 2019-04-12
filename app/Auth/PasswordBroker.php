<?php

namespace App\Auth;

use Closure;
use Illuminate\Support\Arr;
use UnexpectedValueException;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Http\Controllers\Api\UserController;

class PasswordBroker extends \Illuminate\Auth\Passwords\PasswordBroker  implements PasswordBrokerContract
{
    /**
     * Send a password reset link to a user.
     *
     * @param  array  $credentials
     * @return string
     */
    public function sendResetLink(array $credentials)
    {
        list($hash, $errors) = api_result(UserController::class, 'generatePasswordHash', [
            'username' => $credentials['username'],
            'email' => $credentials['email']
        ], 'hash');
        
        if(!empty($errors)){
            return (array)$errors;
        }
        
        unset($credentials['email']);
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        $user->sendPasswordResetNotification($hash);

        return static::RESET_LINK_SENT;
    }
}

