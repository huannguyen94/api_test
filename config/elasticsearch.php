<?php


return [
    'hosts' => [
        [
            "host"   => env("ElasticSearch_HOST","localhost"),
            "port"   => env("ElasticSearch_PORT",9200),
            "scheme" => "http",
            "user"   => "elastic",
            "pass"   => "elastic@elastic@123"
        ]
    ]
];
