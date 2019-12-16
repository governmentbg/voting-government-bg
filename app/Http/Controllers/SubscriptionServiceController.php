<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Libraries\SubscriptionService;

class SubscriptionServiceController extends Controller
{
    private $server = null;

    public function __construct()
    {
 
    }

    public function sendSubscription(Request $request)
    {
        $wsdl = storage_path('app/schema/SubscriptionService.wsdl');
        $this->server = new \SoapServer($wsdl);

        $this->server->setObject(new SubscriptionService());
        
        $this->server->handle();
    }

    public function test()
    {
        //test client action
        ini_set('soap.wsdl_cache_enabled',0); //TODO only for testing
        ini_set('soap.wsdl_cache_ttl',0);
        try{
        
            //$client = new \SoapClient(storage_path('app/schema/SubscriptionService.wsdl'), ['trace' => 1, 'exceptions' => 1]);          
            //$client->__setLocation(route('SendSubscription'));

            $client = new \SoapClient(null, ['trace' => 1, 'exceptions' => 1, 'uri' => url('/SubscriptionService/convert'), 'location' => url('/SubscriptionService/convert')]);

            $client->convert();
            dump($client->__getLastRequest(), $client->__getLastResponse());
        }
        catch(SoapFault $fault){
            die("SOAP Fault:<br />fault code: {$fault->faultcode}, fault string: {$fault->faultstring}");
        }
    }
}

