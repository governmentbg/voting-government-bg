<?php

namespace App\SOAPClasses;

/**
 * Description of SendSubscriptionRequest
 *
 * @author doncho
 */
class SendSubscriptionRequest
{
    /** @var string|null Време на генериране на пакета */
    public $MessageTime;

    /** @var string|null Действие */
    public $Operation;

    public $SubjectUICs;

    public $Event;

    public $StateOfPlay;

    public $Attachments;
}
