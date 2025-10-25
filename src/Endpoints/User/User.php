<?php

namespace RainYun\Endpoints\User;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use RainYun\Endpoints\User\Resources\UserInfo;

/**
 * User namespace - provides access to user API resources.
 *
 * This class acts as a factory for accessing specific API resources
 * under the /user namespace.
 */
class User
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
     * Access the UserInfo API resource.
     *
     * Example:
     * ```php
     * $result = $client->user()->info()->get();
     * ```
     *
     * @return UserInfo UserInfo API resource
     */
    public function info(): UserInfo
    {
        return new UserInfo(
            $this->httpClient,
            $this->requestFactory,
            $this->uriFactory,
            $this->baseUrl,
            $this->apiKey
        );
    }
}
