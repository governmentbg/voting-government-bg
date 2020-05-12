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
        if (!$this->email) {
            return $this->organisation->email;
        }

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
        try {
            $this->notify(new ResetPasswordNotification($token));
        } catch (\Exception $e) {
            logger()->error('Send password reset email error: '. $e->getMessage());
            return false;
        }

        return true;
    }
}

