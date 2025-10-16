<?php

namespace RainYun\Endpoints\Pub;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriFactoryInterface;
use RainYun\Options;

class Pub
{
    private HttpClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private UriFactoryInterface $uriFactory;
    private string $baseUrl;

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
     * GET request to public endpoints like `status`.
     *
     * @param string $path
     * @param Options|null $options
     * @return PubCollection
     */
    public function get(string $path = 'status', ?Options $options = null): PubCollection
    {
        $uri = $this->uriFactory->createUri($this->baseUrl);
        $existingPath = rtrim($uri->getPath(), '/');
        $newPath = ($existingPath === '' ? '' : $existingPath) . '/' . ltrim($path, '/');
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
