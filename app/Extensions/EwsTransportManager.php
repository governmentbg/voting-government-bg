<?php

namespace App\Extensions;

use Illuminate\Mail\TransportManager;

/**
 * Description of EwsTransportManager
 *
 */
class EwsTransportManager extends TransportManager
{
    protected function createEwsDriver ()
    {
        $config = $this->app['config']->get('services.ews', []);

        return new EwsTransport();
    }

    protected function configureEwsDriver($transport, $config)
    {
       
    }
}
