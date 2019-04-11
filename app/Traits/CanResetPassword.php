<?php

namespace App\Http\Controllers\Traits;

use App\Auth\Notifications\ResetPassword as ResetPasswordNotification;

trait CanResetPassword
{

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }
    
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @param  string  $affiliate
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}

