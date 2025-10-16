<?php

namespace RainYun\Endpoints\Pub\Resources;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriFactoryInterface;
use RainYun\Endpoints\Pub\PubCollection;
use RainYun\Endpoints\Pub\PubGetOptions;

/**
 * Status API resource.
 *
 * Provides methods to interact with the /status endpoint.
 */
class Status
{
    private HttpClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private UriFactoryInterface $uriFactory;
    private string $baseUrl;

    /**
     * @param HttpClientInterface $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param UriFactoryInterface $uriFactory
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
     * Get status information.
     *
     * Example:
     * ```php
     * $options = PubGetOptions::make()
     *     ->filter('Product', 'rcs')
     *     ->page(1)
     *     ->perPage(20);
     * $result = $client->pub()->status()->get($options);
     * ```
     *
     * @param PubGetOptions|null $options Query options for filtering, sorting, and pagination
     * @return PubCollection Response collection containing status data
     */
    public function get(?PubGetOptions $options = null): PubCollection
    {
        $uri = $this->uriFactory->createUri($this->baseUrl);
        $existingPath = rtrim($uri->getPath(), '/');
        $newPath = ($existingPath === '' ? '' : $existingPath) . '/status';
        $uri = $uri->withPath($newPath);

        $query = [];
        if ($options) {
            $query['options'] = $options->toJson();
        }
        if (!empty($query)) {
            $uri = $uri->withQuery(http_build_query($query));
        }

        $request = $this->requestFactory->createRequest('GET', $uri)
            ->withHeader('Accept', 'application/json')
            ->withHeader('User-Agent', 'rainyun-php-sdk/0.1');

        $response = $this->httpClient->sendRequest($request);
        return $this->decodeResponse($response);
    }

    /**
     * Decode HTTP response into PubCollection.
     *
     * @param ResponseInterface $response
     * @return PubCollection
     */
    private function decodeResponse(ResponseInterface $response): PubCollection
    {
        $body = (string) $response->getBody();
        $contentType = $response->getHeaderLine('Content-Type');
        if (stripos($contentType, 'json') !== false) {
            $decoded = json_decode($body, true);
            return new PubCollection(is_array($decoded) ? $decoded : ['value' => $decoded]);
        }
        return new PubCollection([
            'raw' => $body,
            'content_type' => $contentType,
            'status' => $response->getStatusCode(),
        ]);
    }
}
