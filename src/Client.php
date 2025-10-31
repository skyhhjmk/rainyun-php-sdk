<?php

namespace RainYun;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use RainYun\Endpoints\Pub\Pub;
use RainYun\Endpoints\User\User;
use RainYun\Endpoints\Product\Product;

class Client
{
    private string $baseUrl;
    private HttpClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private UriFactoryInterface $uriFactory;
    private ?string $apiKey;

    public function __construct(
        HttpClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        UriFactoryInterface $uriFactory,
        string $baseUrl = 'https://api.v2.rainyun.com',
        ?string $apiKey = null
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->uriFactory = $uriFactory;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey;
    }

    public function pub(): Pub
    {
        return new Pub($this->httpClient, $this->requestFactory, $this->uriFactory, $this->baseUrl);
    }

    /**
     * Access the User namespace.
     *
     * Example:
     * ```php
     * $result = $client->user()->info()->get();
     * ```
     *
     * @return User User namespace
     */
    public function user(): User
    {
        return new User($this->httpClient, $this->requestFactory, $this->uriFactory, $this->baseUrl, $this->apiKey);
    }

    /**
     * Access the Product namespace.
     *
     * Example:
     * ```php
     * $result = $client->product()->rcs()->get();
     * ```
     *
     * @return Product Product namespace
     */
    public function product(): Product
    {
        return new Product($this->httpClient, $this->requestFactory, $this->uriFactory, $this->baseUrl, $this->apiKey);
    }
}
