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
        if(isset($this->data->SendSubscriptionRequest)){
            foreach((array)$this->data->SendSubscriptionRequest->SubjectUICs as $subjectUIC)
            {
                if($subjectUIC->Status == BulstatRegister::STATUS_INACTIVE || $subjectUIC->Status == BulstatRegister::STATUS_DELETED){
                    BulstatRegister::where('eik', $subjectUIC->UIC)->update(['status' => $subjectUIC->Status, 'status_date' => date('Y-m-d H:i:s')]);
                }

                if($subjectUIC->Status == BulstatRegister::STATUS_ACTIVE){
                    $data = XMLParserBulstat::getRelevantFields($this->data->SendSubscriptionRequest->StateOfPlay);

                    if(!empty($data) && $data){
                        //$eik = $subjectUIC->UIC;
                        $eik = $data['eik'];
                        unset($data['eik']);
                        BulstatRegister::updateOrCreate(['eik' => $eik], $data);
                    }
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

        $requestRec = SubscriptionRequest::where('UID', $this->data->UID)->first();
        if($requestRec){
            $requestRec->update(['status' => SubscriptionRequest::STATUS_ERROR]);
        }
    }
}
