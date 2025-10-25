<?php

namespace RainYun\Endpoints\User\Resources;

use Psr\Http\Client\ClientExceptionInterface;
use RainYun\Endpoints\AbstractResource;
use RainYun\Endpoints\User\UserCollection;

/**
 * UserInfo API resource.
 *
 * Provides methods to interact with the /user/ endpoint.
 */
class UserInfo extends AbstractResource
{
    /**
     * Get user data.
     *
     * Requires API key authentication via x-api-key header.
     *
     * Example:
     * ```php
     * $result = $client->user()->info()->get();
     * ```
     *
     * @return UserCollection Response collection containing user data
     * @throws ClientExceptionInterface
     */
    public function get(): UserCollection
    {
        $uri = $this->buildUri('/user/');
        $request = $this->createGetRequest($uri, true); // Requires authentication
        $response = $this->httpClient->sendRequest($request);
        
        return parent::decodeResponse($response, UserCollection::class);
    }
}
