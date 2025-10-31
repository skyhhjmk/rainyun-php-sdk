<?php

namespace RainYun\Endpoints\Product;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use RainYun\Endpoints\Product\Resources\Rcs;

/**
 * Product namespace - provides access to product API resources.
 *
 * This class acts as a factory for accessing specific API resources
 * under the /product namespace.
 */
class Product
{
    private HttpClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private UriFactoryInterface $uriFactory;
    private string $baseUrl;
    private ?string $apiKey;

    /**
     * @param HttpClientInterface $httpClient PSR-18 HTTP client
     * @param RequestFactoryInterface $requestFactory PSR-17 request factory
     * @param UriFactoryInterface $uriFactory PSR-17 URI factory
     * @param string $baseUrl Base API URL
     * @param string|null $apiKey API key for authentication
     */
    public function __construct(
        HttpClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        UriFactoryInterface $uriFactory,
        string $baseUrl,
        ?string $apiKey = null
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->uriFactory = $uriFactory;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey;
    }

    /**
     * Access the RCS (Cloud Server) API resource.
     *
     * Example:
     * ```php
     * $result = $client->product()->rcs()->get();
     * ```
     *
     * @return Rcs RCS API resource
     */
    public function rcs(): Rcs
    {
        return new Rcs(
            $this->httpClient,
            $this->requestFactory,
            $this->uriFactory,
            $this->baseUrl,
            $this->apiKey
        );
    }
}
