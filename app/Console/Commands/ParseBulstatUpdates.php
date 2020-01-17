<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\SubscriptionRequest;
use App\Libraries\XMLParserBulstat;

class ParseBulstatUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:bulstatUpdates';

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
        while($subscriptionRequest = SubscriptionRequest::bulstat()->where('status', SubscriptionRequest::STATUS_ERROR)
                    ->orderBy('created_at', 'asc')->first()){

            try{
                $parser = new XMLParserBulstat();
                $parser->loadString($subscriptionRequest->request_xml);

                dd($parser->getParsedData());
                if(!$parser->loadString($subscriptionRequest->request_xml)){
                    $this->info('Could not parse xml for Request: ' . $subscriptionRequest->uid);
                    continue;
                }
                $this->info($subscriptionRequest->uid);

                $data = $this->parser->getParsedData();
                foreach($data as $key => $org) {
                    $eik = $org['eik'];
                    unset($org['eik']);
                    BulstatRegister::updateOrCreate(['eik' => $eik], $org);
                }
                $this->info(''); //add new row
            }
            catch(\Exception $e){
                $this->error('UID: ' . $subscriptionRequest->uid);
                $this->error($ $e->getMessage());
                return;
            }

            $subscriptionRequest->update('status', SubscriptionRequest::STATUS_NEW);
        }
    }
}
