<?php

namespace App\Extensions;

use Illuminate\Mail\Transport\Transport;

use \jamesiarmes\PhpEws\Client;
use \jamesiarmes\PhpEws\Request\CreateItemType;
use \jamesiarmes\PhpEws\Request\SendItemType;

use \jamesiarmes\PhpEws\ArrayType\ArrayOfRecipientsType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAllItemsType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseItemIdsType;

use \jamesiarmes\PhpEws\Enumeration\BodyTypeType;
use \jamesiarmes\PhpEws\Enumeration\MessageDispositionType;
use \jamesiarmes\PhpEws\Enumeration\ResponseClassType;
use \jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;

use \jamesiarmes\PhpEws\Type\BodyType;
use \jamesiarmes\PhpEws\Type\EmailAddressType;
use \jamesiarmes\PhpEws\Type\MessageType;
use \jamesiarmes\PhpEws\Type\SingleRecipientType;
use \jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use \jamesiarmes\PhpEws\Type\ItemIdType;
use \jamesiarmes\PhpEws\Type\TargetFolderIdType;


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
        $messageId = null;
        $messageChangeKey = null;

        $client = new Client(config('mail.host'), config('mail.username'), config('mail.password'));

        $request = new CreateItemType();
        $request->Items = new NonEmptyArrayOfAllItemsType();
        // Message type can be send_only which will not create a draft, or save_only which will create a draft and not send
        $request->MessageDisposition = MessageDispositionType::SEND_ONLY;

        // Create the message.
        $messageData = new MessageType();
        $messageData->Subject = $message->getSubject();
        $messageData->ToRecipients = new ArrayOfRecipientsType();

        // Set the sender.
        $messageData->From = new SingleRecipientType();
        $messageData->From->Mailbox = new EmailAddressType();
        $messageData->From->Mailbox->EmailAddress = array_keys($message->getFrom())[0];

        // Set the recipient.
        $recipient = new EmailAddressType();
        $recipient->EmailAddress = array_keys($message->getTo())[0];
        $messageData->ToRecipients->Mailbox[] = $recipient;

        // Set the message body.
        $messageData->Body = new BodyType();
        $messageData->Body->BodyType = BodyTypeType::HTML;
        $messageData->Body->_ = $message->getBody();

        $request->Items->Message[] = $messageData;
        $response = $client->CreateItem($request);

        $response_messages = $response->ResponseMessages->CreateItemResponseMessage;

        foreach ($response_messages as $response_message) {
            if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                $code = $response_message->ResponseCode;
                $messageText = $response_message->MessageText;

                error_log(var_export($code .':'. $message, true));
                continue;
            }

            foreach ($response_message->Items->Message as $item) {
                $messageId =  $item->ItemId->Id;
                $messageChangeKey = $item->ItemId->ChangeKey;
            }
        }

        $request = new SendItemType();
        $request->SaveItemToFolder = true;
        $request->ItemIds = new NonEmptyArrayOfBaseItemIdsType();

        $item = new ItemIdType();
        $item->Id = $messageId;
        $item->ChangeKey = $messageChangeKey;
        $request->ItemIds->ItemId[] = $item;

        $send_folder = new TargetFolderIdType();
        $send_folder->DistinguishedFolderId = new DistinguishedFolderIdType();
        $send_folder->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::SENT;
        $request->SavedItemFolderId = $send_folder;

        $response = $client->SendItem($request);

        $response_messages = $response->ResponseMessages->SendItemResponseMessage;

        foreach ($response_messages as $response_message) {
            if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                $code = $response_message->ResponseCode;
                $message = $response_message->MessageText;
                error_log(var_export($code .':'. $message, true));
                continue;
            }
        }
    }
}
