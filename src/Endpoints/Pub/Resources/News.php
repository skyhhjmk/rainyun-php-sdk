<?php

namespace RainYun\Endpoints\Pub\Resources;

use Psr\Http\Client\ClientExceptionInterface;
use RainYun\Endpoints\AbstractResource;
use RainYun\Endpoints\Pub\Collection\NewsCollection;

/**
 * News API resource.
 *
 * Provides methods to interact with the /news endpoint.
 */
class News extends AbstractResource
{
    /**
     * Get forum news/announcements.
     *
     * Example:
     * ```php
     * $result = $client->pub()->news()->get();
     * foreach ($result->data as $item) {
     *     echo $item->Title . "\n";
     * }
     * ```
     *
     * @return NewsCollection Response collection containing news data
     * @throws ClientExceptionInterface
     */
    public function get(): NewsCollection
    {
        $uri = $this->buildUri('/news');
        $request = $this->createGetRequest($uri);
        $response = $this->httpClient->sendRequest($request);
        
        return parent::decodeResponse($response, NewsCollection::class);
    }
}
