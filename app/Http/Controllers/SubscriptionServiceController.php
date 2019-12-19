<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Libraries\SubscriptionService;
use App\SOAPClasses;

class SubscriptionServiceController extends Controller
{
    const CLASS_MAP = [
        'SendSubscription' => SOAPClasses\SendSubscription::class,
        'SendSubscriptionRequest' => SOAPClasses\SendSubscriptionRequest::class,
    ];
    
    private $server = null;

    public function __construct()
    {
        //
    }

    public function sendSubscription(Request $request)
    {
        $wsdl = storage_path('app/schema/SubscriptionService.wsdl');
        $this->server = new \SoapServer($wsdl, ['classmap' => self::CLASS_MAP]);

        //$this->server->setObject(new SubscriptionService());
        $this->server->setClass(SubscriptionService::class);

        $response = new Response();
        $response->headers->set("Content-Type","text/xml; charset=utf-8");

        ob_start();
        $this->server->handle();
        $response->setContent(ob_get_clean());

        return $response;
    }

    /**
     * Test SOAP client action.
     * @return type
     */
    public function test()
    {
        if(!\App::environment(['local'])){
            return abort(404);
        }

        //test client action
        ini_set('soap.wsdl_cache_enabled',0);
        ini_set('soap.wsdl_cache_ttl',0);
        try{
            $client = new \SoapClient(storage_path('app/schema/SubscriptionService.wsdl'), ['trace' => 1, 'exceptions' => 1,
                'stream_context' => stream_context_create([
                        'ssl' => [
                            'verify_peer'      => false,
                            'verify_peer_name' => false,
                        ],
                        'http' => [
                            'timeout' => 30,
                        ]
                    ])
                ]);

            $res = $client->SendSubscription(['UID' => str_random(15), 'SendSubscriptionRequest' => ['MessageTime' => '12', 'Operation' => '', 'Event' => '', 'StateOfPlay' => '']]);
            dump($client->__getLastRequest(), $client->__getLastResponse(),$res, $client->__getLastRequestHeaders(), $client->__getLastResponseHeaders());
            //$client->__getTypes()
            return response('ok');
        }
        catch(\SoapFault $fault){
            die("SOAP Fault:<br />fault code: {$fault->faultcode}, fault string: {$fault->faultstring}");
        }
    }
}

