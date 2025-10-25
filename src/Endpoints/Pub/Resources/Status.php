<?php

namespace RainYun\Endpoints\Pub\Resources;

use RainYun\Endpoints\AbstractResource;
use RainYun\Endpoints\Pub\PubCollection;
use RainYun\Endpoints\Pub\PubGetOptions;

/**
 * Status API resource.
 *
 * Provides methods to interact with the /status endpoint.
 */
class Status extends AbstractResource
{

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
        $query = [];
        if ($options) {
            $query['options'] = $options->toJson();
        }

        $uri = $this->buildUri('/status', $query);
        $request = $this->createGetRequest($uri);
        $response = $this->httpClient->sendRequest($request);
        
        return parent::decodeResponse($response, PubCollection::class);
    }
}
