<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Libraries;

use Monolog\Logger;

/**
 * Description of LogrProcessor
 *
 * @author doncho
 */
class LogProcessor
{
    /**
    * Customize the given logger instance.
    *
    * @param  \Illuminate\Log\Logger  $logger
    * @return void
    */
    public function __invoke($logger)
    {
        collect($logger->getHandlers())->each(function ($handler) {
            $handler->pushProcessor(new \Monolog\Processor\GitProcessor());
            $handler->pushProcessor(new \Monolog\Processor\WebProcessor());
            $handler->pushProcessor(new \Monolog\Processor\IntrospectionProcessor(Logger::DEBUG, ['Illuminate\\Log', 'Exceptions\\Handler', 'Illuminate\\Routing\\Pipeline']));
            $handler->pushProcessor(new \Monolog\Processor\MemoryPeakUsageProcessor());

            $handler->pushProcessor(function ($record) {
                $connection = config('database.default');
                $record['extra']['dbname'] = config('database.connections.' . $connection . '.database');
                $record['extra']['dbhost'] = config('database.connections.' . $connection . '.host');
                return $record;
            });
        });
    }
}
