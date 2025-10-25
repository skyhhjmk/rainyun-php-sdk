<?php

namespace RainYun\Endpoints;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriFactoryInterface;
use RainYun\Collection;

/**
 * Abstract base class for API resource endpoints.
 *
 * Provides common functionality for HTTP client, request/URI factories,
 * and response decoding to reduce code duplication across resources.
 */
abstract class AbstractResource
{
    protected HttpClientInterface $httpClient;
    protected RequestFactoryInterface $requestFactory;
    protected UriFactoryInterface $uriFactory;
    protected string $baseUrl;
    protected ?string $apiKey = null;

    /**
     * @param HttpClientInterface $httpClient PSR-18 HTTP client
     * @param RequestFactoryInterface $requestFactory PSR-17 request factory
     * @param UriFactoryInterface $uriFactory PSR-17 URI factory
     * @param string $baseUrl Base API URL
     * @param string|null $apiKey Optional API key for authentication
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
     * Build a URI for the given path.
     *
     * @param string $path Endpoint path (e.g., '/status')
     * @param array<string, mixed> $queryParams Query parameters
     * @return \Psr\Http\Message\UriInterface
     */
    protected function buildUri(string $path, array $queryParams = [])
    {
        $uri = $this->uriFactory->createUri($this->baseUrl);
        $existingPath = rtrim($uri->getPath(), '/');
        $newPath = ($existingPath === '' ? '' : $existingPath) . $path;
        $uri = $uri->withPath($newPath);

        if (!empty($queryParams)) {
            $uri = $uri->withQuery(http_build_query($queryParams));
        }

        return $uri;
    }

    /**
     * Create a GET request with standard headers.
     *
     * @param \Psr\Http\Message\UriInterface $uri Request URI
     * @param bool $requireAuth Whether to include API key authentication
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function createGetRequest($uri, bool $requireAuth = false)
    {
        $request = $this->requestFactory->createRequest('GET', $uri)
            ->withHeader('Accept', 'application/json')
            ->withHeader('User-Agent', 'rainyun-php-sdk/0.1');
        
        if ($requireAuth && $this->apiKey !== null) {
            $request = $request->withHeader('x-api-key', $this->apiKey);
        }
        
        return $request;
    }

    /**
     * Decode HTTP response into a Collection.
     *
     * @param ResponseInterface $response HTTP response
     * @param string $collectionClass Collection class name to instantiate
     * @return Collection
     */
    protected function decodeResponse(ResponseInterface $response, string $collectionClass): Collection
    {
        $body = (string) $response->getBody();
        $contentType = $response->getHeaderLine('Content-Type');
        
        if (stripos($contentType, 'json') !== false) {
            $decoded = json_decode($body, true);
            return new $collectionClass(is_array($decoded) ? $decoded : ['value' => $decoded]);
        }
        
        return new $collectionClass([
            'raw' => $body,
            'content_type' => $contentType,
            'status' => $response->getStatusCode(),
        ]);
    }
}
