<?php

namespace RainYun\Endpoints\Pub\Resources;

use Psr\Http\Client\ClientExceptionInterface;
use RainYun\Collection;
use RainYun\Endpoints\AbstractResource;

/**
 * ShortParams API resource.
 *
 * Endpoint: /short_params
 * - POST to create short params
 * - GET to parse short params back to original payload
 */
class ShortParams extends AbstractResource
{
    /**
     * Create a short params entry.
     *
     * POST https://api.v2.rainyun.com/short_params
     * Body JSON: {
     *   "name": "",            // default empty string
     *   "action": "plan_share", // default "plan_share"
     *   "params": "..."         // REQUIRED - user supplied
     * }
     *
     * @param string|array|Collection|object $params The content to store; arrays/Collection/objects will be JSON-encoded, strings are used as-is
     * @param string $name Default "" as per API
     * @param string $action Default "plan_share" as per API
     * @return Collection Envelope collection, e.g. { code: 200, data: "6k2Ej7" }
     * @throws ClientExceptionInterface
     */
    public function create($params, string $name = '', string $action = 'plan_share'): Collection
    {
        // Normalize params to string
        if ($params instanceof Collection) {
            $params = $params->toArray();
        }
        if (is_array($params)) {
            $params = json_encode($params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } elseif (is_object($params)) {
            if ($params instanceof \JsonSerializable) {
                $params = json_encode($params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } elseif (method_exists($params, 'toArray')) {
                $arr = $params->toArray();
                $params = json_encode($arr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            } else {
                $params = json_encode(get_object_vars($params), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        } elseif (!is_string($params)) {
            // For scalars, cast to string
            $params = (string) $params;
        }

        $body = [
            'name' => $name,
            'action' => $action,
            'params' => $params,
        ];

        $uri = $this->buildUri('/short_params');
        $request = $this->requestFactory->createRequest('POST', $uri)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $response = $this->httpClient->sendRequest($request);
        return parent::decodeResponse($response, Collection::class);
    }

    /**
     * Parse a short params name back to the original payload.
     *
     * GET https://api.v2.rainyun.com/short_params?name=XXXXXX
     * Response: { code: 200, data: base64_string }
     *
     * This method decodes the base64 payload and returns a Collection when the payload is JSON,
     * or a Collection with ['value' => string] when the payload is not JSON.
     *
     * Example usages:
     * $result = $client->pub()->shortParams()->parse('i3DAjD');
     * // If payload is JSON: $result->icon_config->url
     * // If payload is plain string: (string)$result or $result->value
     *
     * @param string $name The short params name returned by create()
     * @return Collection Decoded payload as a Collection (or with 'value' key if not JSON)
     * @throws ClientExceptionInterface
     */
    public function parse(string $name): Collection
    {
        $uri = $this->buildUri('/short_params', ['name' => $name]);
        $request = $this->createGetRequest($uri);
        $response = $this->httpClient->sendRequest($request);

        // Decode envelope first
        $envelope = parent::decodeResponse($response, Collection::class);
        $b64 = $envelope->data ?? '';

        if (!is_string($b64) || $b64 === '') {
            return new Collection(['value' => $b64]);
        }

        $decoded = base64_decode($b64, true);
        if ($decoded === false) {
            // Not valid base64, return raw string
            return new Collection(['value' => $b64]);
        }

        // Try JSON decode
        $json = json_decode($decoded, true);
        if (json_last_error() === JSON_ERROR_NONE && (is_array($json) || is_object($json))) {
            return new Collection(is_array($json) ? $json : (array) $json);
        }

        // Return plain string wrapped
        return new Collection(['value' => $decoded]);
    }
}