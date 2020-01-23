<?php

namespace App\Jobs;

use App\BulstatRegister;
use App\SubscriptionRequest;
use App\Libraries\XMLParserBulstat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateBulstatRegister implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //UIC - statuses
    const STATUS_ACTIVE = 'Y'; //Y => Актуален код
    const STATUS_DELETED = 'D'; //D => Изтрит код
    const STATUS_INACTIVE = 'N'; //N => Неактуален код

    private $data;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->serviceHasErrors()){
            //if previous subcription request were not parsed correctly don't parse current one
            $requestRec = SubscriptionRequest::bulstat()->where('UID', $this->data->UID)->first();
            if($requestRec){
                //set ERROR status
                $requestRec->update(['status' => SubscriptionRequest::STATUS_ERROR]);
            }
            return;
        }

        if(isset($this->data->SendSubscriptionRequest)){
            $parser = new XMLParserBulstat();
            foreach((array)$this->data->SendSubscriptionRequest->SubjectUICs as $subjectUIC)
            {
                //inactive or deleted UICs
                if($subjectUIC->Status == self::STATUS_INACTIVE || $subjectUIC->Status == self::STATUS_DELETED){
                    $status = $subjectUIC->Status == self::STATUS_INACTIVE ? BulstatRegister::STATUS_INACTIVE : BulstatRegister::STATUS_ARCHIVED;
                    BulstatRegister::where('eik', $subjectUIC->UIC)->update(['status' => $status, 'status_date' => date('Y-m-d H:i:s')]);
                }
            }

            $data = $parser->getRelevantFields($this->data->SendSubscriptionRequest->StateOfPlay);
            if(!empty($data) && $data){
                //$eik = $subjectUIC->UIC;
                $eik = $data['eik'];
                unset($data['eik']);
                BulstatRegister::updateOrCreate(['eik' => $eik], $data);

                $requestRec = SubscriptionRequest::bulstat()->where('UID', $this->data->UID)->first();
                if($requestRec){
                    $requestRec->update(['status' => SubscriptionRequest::STATUS_PROCESSED]);
                }
            }
        }
    }

    /**
     * The job failed to process.
     *
     * @param  Exception $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        logger()->error($exception->getMessage() . ' UID: ' . $this->data->UID);

        $requestRec = SubscriptionRequest::bulstat()->where('UID', $this->data->UID)->first();
        if($requestRec){
            $requestRec->update(['status' => SubscriptionRequest::STATUS_ERROR]);
        }
    }

    /**
     * Check if there are erros in the past 5 subscription requests.
     * @return boolean
     */
    private function serviceHasErrors()
    {
        return SubscriptionRequest::bulstat()->orderBy('created_at', 'desc')->take(5)->max('status') == SubscriptionRequest::STATUS_ERROR;
    }
}
