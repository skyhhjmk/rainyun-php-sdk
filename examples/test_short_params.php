<?php

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use RainYun\Client;

$http = new GuzzleClient(['verify' => false]);
$factory = new HttpFactory();

$client = new Client($http, $factory, $factory, 'https://api.v2.rainyun.com');

$payload = [
    'icon_config' => [
        'url' => 'https://example.com/icon.png',
        'size' => 128,
    ],
    'oslist' => 'Debian',
];

$created = $client->pub()->shortParams()->create($payload);

echo "Created short params name: " . $created->data . "\n";

// Parse it back
$decoded = $client->pub()->shortParams()->parse($created->data);

echo "Decoded url: " . $decoded->icon_config->url . "\n";
