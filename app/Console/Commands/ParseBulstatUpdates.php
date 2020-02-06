<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\SubscriptionRequest;
use App\Libraries\XMLParserBulstat;
use App\BulstatRegister;

class ParseBulstatUpdates extends Command
{
    //UIC - statuses
    const STATUS_ACTIVE = 'Y'; //Y => Актуален код
    const STATUS_DELETED = 'D'; //D => Изтрит код
    const STATUS_INACTIVE = 'N'; //N => Неактуален код

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:bulstatUpdates {--a|all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $subscriptionRequest = true;

        if($this->option('all')){
            $filter['uid'] = '0';
        }
        else{
            $filter['status'] = SubscriptionRequest::STATUS_ERROR;
        }

        while($subscriptionRequest = SubscriptionRequest::bulstat()->filter($filter)
                    ->orderBy('uid', 'asc')->first()){

            try{
                $parser = new XMLParserBulstat();

                if(!$parser->loadString($subscriptionRequest->request_xml)){
                    $this->info('Could not parse xml for Request: ' . $subscriptionRequest->uid);
                    continue;
                }
                $this->info('Processing subscription service update with UID: ' . $subscriptionRequest->uid);

                foreach((array)$parser->getUicUpdates() as $subjectUIC)
                {
                    //inactive or deleted UICs
                    if(isset($subjectUIC->Status) && ($subjectUIC->Status == self::STATUS_INACTIVE || $subjectUIC->Status == self::STATUS_DELETED)){
                        $status = (string)$subjectUIC->Status == self::STATUS_INACTIVE ? BulstatRegister::STATUS_INACTIVE : BulstatRegister::STATUS_ARCHIVED;
                        BulstatRegister::where('eik', (string)$subjectUIC->UIC->UIC)->update(['status' => $status, 'status_date' => date('Y-m-d H:i:s')]);
                    }
                }

                $data = $parser->getParsedData();
                foreach($data as $key => $org) {
                    $eik = $org['eik'];
                    unset($org['eik']);
                    BulstatRegister::updateOrCreate(['eik' => $eik], $org);
                    $this->info('Update/created record with eik: ' . $eik);
                }
                $this->info(''); //add new row
            }
            catch(\Exception $e){
                $this->error('UID: ' . $subscriptionRequest->uid);
                $this->error($ $e->getMessage());
                return;
            }

            $subscriptionRequest->update(['status' => SubscriptionRequest::STATUS_PROCESSED]);

            if($this->option('all')){
                $filter['uid'] = $subscriptionRequest->uid;
            }
        }
    }
}
