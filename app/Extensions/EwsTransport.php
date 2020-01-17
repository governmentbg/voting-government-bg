<?php

namespace App\Extensions;

use Illuminate\Mail\Transport\Transport;

/**
 * Description of EwsTransport
 *
 */
class EwsTransport extends Transport
{
    
    public function send(\Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);
        
        $message->setBcc([]);

        //$message->getSubject(),
        //$message->getBody()
        //$message->getTo()

        //TODO send message

        $this->sendPerformed();

        return $this->numberOfRecipients($message);
    }
}
