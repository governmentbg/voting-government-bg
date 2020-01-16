<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Libraries\XMLParser;
use App\SubscriptionRequest;
use App\TradeRegister;
use App\ActionsHistory;

use Carbon\Carbon;
use \jamesiarmes\PhpEws\Client;
use \jamesiarmes\PhpEws\Type\AndType;
use \jamesiarmes\PhpEws\Type\ItemIdType;
use \jamesiarmes\PhpEws\Request\GetItemType;
use \jamesiarmes\PhpEws\Type\RestrictionType;
use \jamesiarmes\PhpEws\Request\FindItemType;
use \jamesiarmes\PhpEws\Type\ConstantValueType;
use \jamesiarmes\PhpEws\Request\GetAttachmentType;
use \jamesiarmes\PhpEws\Type\ItemResponseShapeType;
use \jamesiarmes\PhpEws\Type\FieldURIOrConstantType;
use \jamesiarmes\PhpEws\Type\IsLessThanOrEqualToType;
use \jamesiarmes\PhpEws\Type\RequestAttachmentIdType;
use \jamesiarmes\PhpEws\Enumeration\ResponseClassType;
use \jamesiarmes\PhpEws\Type\PathToUnindexedFieldType;
use \jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use \jamesiarmes\PhpEws\Type\IsGreaterThanOrEqualToType;
use \jamesiarmes\PhpEws\Enumeration\UnindexedFieldURIType;
use \jamesiarmes\PhpEws\Enumeration\DefaultShapeNamesType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseItemIdsType;
use \jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfRequestAttachmentIdsType;

class ImportFromMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:emailFiles {startDate?} {endDate?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Connects to amsvoting mailbox, reads mails, downloads zip files, processes, imports unimported xmls and deletes files locally after they have been imported.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $attachmentsArray = [];
        $success = false;

        try {
            $this->info('Connecting to email and reading files. Please wait.');

            $client = new Client(config('mail.importserver'), config('mail.importuser'), config('mail.importpassword'));

            //input arguments
            $startDate = $this->argument('startDate') ? Carbon::parse($this->argument('startDate')) : Carbon::now()->startOfDay()->subWeek(1);
            $endDate = $this->argument('endDate') ? Carbon::parse($this->argument('endDate'))->endOfDay() : Carbon::now()->endOfDay();

            $request = new FindItemType();
            $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();

            $request->ItemShape = new ItemResponseShapeType();
            $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;

            $folderId = new DistinguishedFolderIdType();
            $folderId->Id = DistinguishedFolderIdNameType::INBOX;
            $request->ParentFolderIds->DistinguishedFolderId[] = $folderId;

            // Build the start date restriction
            $greaterThan = new IsGreaterThanOrEqualToType();
            $greaterThan->FieldURI = new PathToUnindexedFieldType();
            $greaterThan->FieldURI->FieldURI = UnindexedFieldURIType::ITEM_DATE_TIME_RECEIVED;
            $greaterThan->FieldURIOrConstant = new FieldURIOrConstantType();
            $greaterThan->FieldURIOrConstant->Constant = new ConstantValueType();
            $greaterThan->FieldURIOrConstant->Constant->Value = $startDate->format('c');

            // Build the end date restriction
            $lessThan = new IsLessThanOrEqualToType();
            $lessThan->FieldURI = new PathToUnindexedFieldType();
            $lessThan->FieldURI->FieldURI = UnindexedFieldURIType::ITEM_DATE_TIME_RECEIVED;
            $lessThan->FieldURIOrConstant = new FieldURIOrConstantType();
            $lessThan->FieldURIOrConstant->Constant = new ConstantValueType();
            $lessThan->FieldURIOrConstant->Constant->Value = $endDate->format('c');

            // Build the restriction
            $request->Restriction = new RestrictionType();
            $request->Restriction->And = new AndType();
            $request->Restriction->And->IsGreaterThanOrEqualTo = $greaterThan;
            $request->Restriction->And->IsLessThanOrEqualTo = $lessThan;

            $response = $client->FindItem($request);
            $response_messages = $response->ResponseMessages->FindItemResponseMessage;

            \DB::beginTransaction();

            foreach ($response_messages as $response_message) {
                if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                    $code = $response_message->ResponseCode;
                    $message = $response_message->MessageText;

                    $this->info('Failed to search for messages with '. $code .':'. $message);
                    continue;
                }

                $items = $response_message->RootFolder->Items->Message;
            }

            $file_destination = storage_path() .'/files';

            if (!file_exists($file_destination)) {
                mkdir($file_destination, 0777, true);
            }

            if (!is_writable($file_destination)) {
                $this->info('Destination '. $file_destination .' is not writable.');
            }

            foreach ($items as $index => $messageDetails) {
                $fileRequest = new GetItemType();
                $fileRequest->ItemShape = new ItemResponseShapeType();
                $fileRequest->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
                $fileRequest->ItemIds = new NonEmptyArrayOfBaseItemIdsType();

                $item = new ItemIdType();
                $item->Id = $messageDetails->ItemId->Id;

                $fileRequest->ItemIds->ItemId[] = $item;
                $fileResponse = $client->GetItem($fileRequest);
                $fileResponseMessages = $fileResponse->ResponseMessages->GetItemResponseMessage;

                foreach ($fileResponseMessages as $response_message) {
                    if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
                        $code = $response_message->ResponseCode;
                        $message = $response_message->MessageText;
                        $this->info('Failed to get message with '. $code .':'. $message);
                        continue;
                    }
                }

                $attachments = array();

                foreach ($fileResponseMessages[0]->Items->Message as $item) {
                    if (empty($item->Attachments)) {
                        continue;
                    }

                    foreach ($item->Attachments->FileAttachment as $attachment) {
                        $attachments[] = $attachment->AttachmentId->Id;
                    }
                }

                $requestAttachment = new GetAttachmentType();
                $requestAttachment->AttachmentIds = new NonEmptyArrayOfRequestAttachmentIdsType();

                foreach ($attachments as $attachment_id) {
                    $id = new RequestAttachmentIdType();
                    $id->Id = $attachment_id;
                    $requestAttachment->AttachmentIds->AttachmentId[] = $id;
                }

                $responseAttachment = $client->GetAttachment($requestAttachment);
                $attachmentResponseMessages = $responseAttachment->ResponseMessages->GetAttachmentResponseMessage;

                foreach ($attachmentResponseMessages as $attachmentResponseMessage) {
                    if ($attachmentResponseMessage->ResponseClass != ResponseClassType::SUCCESS) {
                        $code = $response_message->ResponseCode;
                        $message = $response_message->MessageText;
                        $this->info('Failed to get attachment with '. $code .':'. $message);

                        continue;
                    }

                    $attachments = $attachmentResponseMessage->Attachments->FileAttachment;

                    foreach ($attachments as $attachment) {
                        //download only zip or rar files
                        if ($attachment->ContentType == 'application/x-zip-compressed' || $attachment->ContentType == 'application/octet-stream') {
                            // Verify that the file has not been imported
                            if (empty(SubscriptionRequest::select('uid')->where('uid', $attachment->AttachmentId->Id)->first())) {
                                $attachmentsArray[] = $attachment->Name;
                                $path = $file_destination . '/' . str_replace(['/','\\',chr(0)], '', $attachment->Name);
                                file_put_contents($path, $attachment->Content);

                                SubscriptionRequest::create([
                                    'uid'         => $attachment->AttachmentId->Id,
                                    'request_xml' => $attachment->Name,
                                    'type'        => SubscriptionRequest::SUB_TYPE_TRADE,
                                    'status'      => 0
                                ]);
                            }
                        }
                    }
                }
            }

            if (!empty($attachmentsArray)) {
                $this->info('');
                $this->info('Unzipping:');

                $barFiles = $this->output->createProgressBar(count($attachmentsArray));
                $barFiles->start();

                foreach ($attachmentsArray as $attIndex => $attName) {
                    //process downloaded files from mail
                    if (strpos($attName, '.zip') == true) {
                        exec('unzip -o '. storage_path() .'/files/'. $attName .' -d '. storage_path() .'/files/');
                    }

                    if (strpos($attName, '.rar') == true) {
                        exec('unrar-free '. storage_path() .'/files/'. $attName .' '. storage_path() .'/files/');
                    }

                    //remove all archive files files from directory
                    exec('rm -rf '. storage_path() .'/files/'.  $attName);
                    $barFiles->advance();
                }

                $barFiles->finish();

                $this->info('');
                $files = $this->searchFilesDirectory(storage_path() .'/files/');
                $bar = $this->output->createProgressBar(count($files));

                $parser = new XMLParser();

                $this->info('');
                $this->info('Importing:');
                $bar->start();

                foreach ($files as $singleFile) {
                    $parser->loadFile($singleFile);
                    $parsedData = $parser->getParsedData();

                    foreach ($parsedData as $singleRow) {
                        try {
                            TradeRegister::updateOrCreate(['eik' => $singleRow['eik']], $singleRow);
                        } catch (\Exception $ex) {
                            $this->error($ex->getMessage());
                            \DB::rollback();
                        }
                    }

                    $bar->advance();
                }

                \DB::commit();

                $bar->finish();

                // delete imported xml files
                exec('rm -rf '. storage_path() .'/files/*');

                $this->info('');
                $this->info('Finished.');
                $success = true;
            }
        } catch (\Exception $e) {
            \DB::rollback();
            $this->error($e->getMessage());
        }

        if ($success) {
            ActionsHistory::create([
                'action'      => ActionsHistory::TYPE_IMPORT_SUCCESS,
                'module'      => ActionsHistory::IMPORTS,
                'user_id'     => \App\User::where('username', 'system')->first()->id,
                'occurrence'  => date('Y-m-d H:i:s'),
                'ip_address'  => '::1',
            ]);

        } else {
            ActionsHistory::create([
                'action'      => ActionsHistory::TYPE_IMPORT_FAILURE,
                'module'      => ActionsHistory::IMPORTS,
                'user_id'     => \App\User::where('username', 'system')->first()->id,
                'occurrence'  => date('Y-m-d H:i:s'),
                'ip_address'  => '::1',
            ]);
        }
    }

    private function searchFilesDirectory($path)
    {
        if (is_file($path)) {
            return [$path];
        }

        $fileSystemIterator = new \FilesystemIterator($path);

        $files = [];
        foreach ($fileSystemIterator as $fileInfo) {
            $type = $fileInfo->getType(); //directory or file
            if ($type == 'file' && $fileInfo->getExtension() == 'xml') {
                $files[] = $fileInfo->getPathname();
            } else if ($type == 'dir') {
                $files = array_merge($files, $this->searchFilesDirectory($fileInfo->getPathname()));
            }
        }

        return $files;
    }
}
