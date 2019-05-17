<?php

return [
    /*
     * Graylog connection configuration.
     */
    'host' => env('GRAYLOG_HOST', '192.168.192.19'),
    'port' => env('GRAYLOG_PORT', '24224'),


    /*
     * Map enviroments to graylog tags. Each enviroment uses different graylog stream.
     */
    'tag' => [
        'test' => 'amsvoting.test',
        'production' => 'amsvoting.production',
        'local' => 'amsvoting.production', //for testing only
    ]
];

