<?php

namespace RainYun\Endpoints\Pub;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use RainYun\Endpoints\Pub\Resources\AppConfig;
use RainYun\Endpoints\Pub\Resources\News;
use RainYun\Endpoints\Pub\Resources\Status;

/**
 * Pub namespace - provides access to public API resources.
 *
 * This class acts as a factory for accessing specific API resources
 * under the /pub namespace.
 */
class Pub
{
    private HttpClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private UriFactoryInterface $uriFactory;
    private string $baseUrl;

    /**
     * @param HttpClientInterface $httpClient PSR-18 HTTP client
     * @param RequestFactoryInterface $requestFactory PSR-17 request factory
     * @param UriFactoryInterface $uriFactory PSR-17 URI factory
     * @param string $baseUrl Base API URL
     */
    public function __construct(
        HttpClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        UriFactoryInterface $uriFactory,
        string $baseUrl
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->uriFactory = $uriFactory;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Access the Status API resource.
     *
     * Example:
     * ```php
     * $result = $client->pub()->status()->get($options);
     * ```
     *
     * @return Status Status API resource
     */
    public function status(): Status
    {
        return new Status(
            $this->httpClient,
            $this->requestFactory,
            $this->uriFactory,
            $this->baseUrl
        );
    }

    /**
     * Access the AppConfig API resource.
     *
     * Example:
     * ```php
     * $result = $client->pub()->appConfig()->get();
     * ```
     *
     * @return AppConfig AppConfig API resource
     */
    public function appConfig(): AppConfig
    {
        return new AppConfig(
            $this->httpClient,
            $this->requestFactory,
            $this->uriFactory,
            $this->baseUrl
        );
    }

    /**
     * Access the News API resource.
     *
     * Example:
     * ```php
     * $result = $client->pub()->news()->get();
     * foreach ($result->data as $item) {
     *     echo $item->Title . "\n";
     * }
     * ```
     *
     * @return News News API resource
     */
    public function news(): News
    {
        return new News(
            $this->httpClient,
            $this->requestFactory,
            $this->uriFactory,
            $this->baseUrl
        );
    }
}
