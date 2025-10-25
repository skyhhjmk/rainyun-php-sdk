<?php

namespace RainYun\Endpoints\Pub\Resources;

use Psr\Http\Client\ClientExceptionInterface;
use RainYun\Endpoints\AbstractResource;
use RainYun\Endpoints\Pub\Collection\AppConfigCollection;

/**
 * AppConfig API resource.
 *
 * Provides methods to interact with the /app_config endpoint.
 */
class AppConfig extends AbstractResource
{

    /**
     * Get app_config information.
     *
     * Example:
     * ```php
     * $result = $client->pub()->app_config()->get($options);
     * ```
     *
     * @return AppConfigCollection Response collection containing app_config data
     * @throws ClientExceptionInterface
     */
    public function get(): AppConfigCollection
    {
        $uri = $this->buildUri('/app_config');
        $request = $this->createGetRequest($uri);
        $response = $this->httpClient->sendRequest($request);
        
        return parent::decodeResponse($response, AppConfigCollection::class);
    }
}
