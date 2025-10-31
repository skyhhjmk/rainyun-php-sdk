<?php

namespace RainYun\Endpoints\Product\Resources;

use Psr\Http\Client\ClientExceptionInterface;
use RainYun\Endpoints\AbstractResource;
use RainYun\Endpoints\Product\Collection\RcsCollection;
use RainYun\Endpoints\Product\RcsGetOptions;

/**
 * RCS (Cloud Server) API resource.
 *
 * Provides methods to interact with the /product/rcs/ endpoint.
 */
class Rcs extends AbstractResource
{
    /**
     * Get RCS (Cloud Server) instances.
     *
     * Requires API key authentication via x-api-key header.
     *
     * Example:
     * ```php
     * // Get all instances
     * $result = $client->product()->rcs()->get();
     *
     * // With options
     * $options = RcsGetOptions::make()
     *     ->isRgpu(false)
     *     ->options(['filter' => 'value']);
     * $result = $client->product()->rcs()->get($options);
     *
     * // Access data
     * if ($result->isSuccess()) {
     *     echo "Total: " . $result->getTotalRecords() . "\n";
     *     foreach ($result->getRecords() as $instance) {
     *         echo "ID: " . $instance->ID . " - " . $instance->HostName . "\n";
     *         echo "Status: " . $instance->Status . "\n";
     *         echo "IP: " . $instance->MainIPv4 . "\n";
     *     }
     *
     *     // Filter by status
     *     $running = $result->getByStatus('running');
     *
     *     // Filter by region
     *     $hkInstances = $result->getByRegion('cn-hk4');
     *
     *     // Get by ID
     *     $instance = $result->getById(220195);
     * }
     * ```
     *
     * @param RcsGetOptions|null $options Query options
     * @return RcsCollection Response collection containing RCS instances
     * @throws ClientExceptionInterface
     */
    public function get(?RcsGetOptions $options = null): RcsCollection
    {
        $queryParams = [];
        
        if ($options !== null) {
            $queryParams = $options->toArray();
        } else {
            // Default options parameter is required
            $queryParams['options'] = '{}';
        }
        
        $uri = $this->buildUri('/product/rcs/', $queryParams);
        $request = $this->createGetRequest($uri, true); // Requires authentication
        $response = $this->httpClient->sendRequest($request);
        
        return parent::decodeResponse($response, RcsCollection::class);
    }
}
