<?php

namespace App\Libraries;

use App\SubscriptionRequest;
use App\Jobs\UpdateBulstatRegister;

class SubscriptionService
{
    const STATUS_ERROR = 'ERROR';
    const STATUS_OK = 'OK';

    public function SendSubscription($subscription)
    {
        try {
            if (isset($subscription->UID)) {
                $savedRequest = SubscriptionRequest::find($subscription->UID);
                if ($savedRequest) {
                    //duplicate request - data already processed
                    logger()->error('Bulstat subscription service notice: Subscription request UID: ' . $subscription->UID . ' already recieved.');
                    return $this->SendSubscriptionResponse(self::STATUS_OK);
                }

                //backup xml data
                $data = ['uid' => $subscription->UID, 'request_xml' => file_get_contents('php://input'), 
                    'type' => SubscriptionRequest::SUB_TYPE_BULSTAT, 'status' => SubscriptionRequest::STATUS_NEW];
                SubscriptionRequest::create($data);

                //process xml data and update register
                UpdateBulstatRegister::dispatch($subscription);
            } else {
                logger()->error('Bulstat subscription service error: "UID" element not found');
                return $this->SendSubscriptionResponse(self::STATUS_ERROR, '"UID" element not found');
            }
        } catch (\Exception $ex) {
            logger()->error('Bulstat subscription service error: ' . $ex->getMessage());
            return $this->SendSubscriptionResponse(self::STATUS_ERROR, $ex->getMessage());
        }

        return $this->SendSubscriptionResponse(self::STATUS_OK);
    }

    public function SendSubscriptionResponse($status, $message = null)
    {
        if (isset($message) && !empty($message)) {
            return ['WSResponse' => ['Status' => $status, 'Message' => $message]];
        }

        return ['WSResponse' => ['Status' => $status]];
    }
}
