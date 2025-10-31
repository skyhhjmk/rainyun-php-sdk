<?php

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use RainYun\Client;
use RainYun\Collection;
use RainYun\Endpoints\Pub\PubGetOptions;

$http = new GuzzleClient(['verify' => false]);
$factory = new HttpFactory();

$client = new Client($http, $factory, $factory, 'https://api.v2.rainyun.com');

$options = PubGetOptions::make()
    ->sort([])
    ->page(1)
    ->perPage(20)
    ->filter('Product', 'rcs');

$result = $client->pub()->appConfig()->get();

echo $result;
