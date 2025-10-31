<?php

namespace RainYun\Tests\Helpers;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MockHttpClient implements ClientInterface
{
    private array $responses = [];
    private array $requests = [];
    private int $currentIndex = 0;

    public function addResponse(ResponseInterface $response): self
    {
        $this->responses[] = $response;
        return $this;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->requests[] = $request;
        
        if (!isset($this->responses[$this->currentIndex])) {
            throw new \RuntimeException('No mock response available');
        }
        
        return $this->responses[$this->currentIndex++];
    }

    public function getRequests(): array
    {
        return $this->requests;
    }

    public function getLastRequest(): ?RequestInterface
    {
        return end($this->requests) ?: null;
    }

    public function reset(): void
    {
        $this->responses = [];
        $this->requests = [];
        $this->currentIndex = 0;
    }
}
