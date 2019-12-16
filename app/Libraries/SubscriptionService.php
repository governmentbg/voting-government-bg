<?php

namespace App\Libraries;

class SubscriptionService
{
    const STATUS_ERROR = 'ERROR';
    const STATUS_OK = 'OK';

    public function SendSubscription($subscription)
    {
        try{
            //save xml data
            $subscription->UID;
            $subscription->SendSubscriptionRequest;
            
        } catch (Exception $ex) {
            return self::SendSubscriptionResponse(self::STATUS_ERROR , $ex->getMessage());
        }

        return self::SendSubscriptionResponse(self::STATUS_OK , 'Success');
    }
    
    public function SendSubscriptionResponse($status, $message = '')
    {
        return ['Status' => $status, 'Message' => $message];
    }
}

