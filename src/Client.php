<?php

namespace RainYun;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use RainYun\Endpoints\Pub\Pub;

class Client
{
    private string $baseUrl;
    private HttpClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private UriFactoryInterface $uriFactory;

    public function __construct(
        HttpClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        UriFactoryInterface $uriFactory,
        string $baseUrl = 'https://api.v2.rainyun.com'
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->uriFactory = $uriFactory;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function pub(): Pub
    {
        return new Pub($this->httpClient, $this->requestFactory, $this->uriFactory, $this->baseUrl);
    }
}
