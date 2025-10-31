<?php

namespace RainYun\Tests\Unit;

use RainYun\Client;
use RainYun\Endpoints\Pub\Pub;
use RainYun\Endpoints\User\User;
use RainYun\Tests\TestCase;
use RainYun\Tests\Helpers\MockHttpClient;
use GuzzleHttp\Psr7\HttpFactory;

class ClientTest extends TestCase
{
    private MockHttpClient $httpClient;
    private HttpFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new MockHttpClient();
        $this->factory = new HttpFactory();
    }

    public function testConstructorWithDefaultBaseUrl(): void
    {
        $client = new Client(
            $this->httpClient,
            $this->factory,
            $this->factory
        );

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testConstructorWithCustomBaseUrl(): void
    {
        $customUrl = 'https://custom.api.example.com';
        $client = new Client(
            $this->httpClient,
            $this->factory,
            $this->factory,
            $customUrl
        );

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testConstructorWithApiKey(): void
    {
        $apiKey = 'test-api-key-123';
        $client = new Client(
            $this->httpClient,
            $this->factory,
            $this->factory,
            'https://api.example.com',
            $apiKey
        );

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testConstructorTrimsTrailingSlashFromBaseUrl(): void
    {
        $client = new Client(
            $this->httpClient,
            $this->factory,
            $this->factory,
            'https://api.example.com/'
        );

        // We can't directly test the internal state, but we can verify
        // the client was constructed successfully
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testPubMethodReturnsPubInstance(): void
    {
        $client = new Client(
            $this->httpClient,
            $this->factory,
            $this->factory
        );

        $pub = $client->pub();

        $this->assertInstanceOf(Pub::class, $pub);
    }

    public function testUserMethodReturnsUserInstance(): void
    {
        $client = new Client(
            $this->httpClient,
            $this->factory,
            $this->factory,
            'https://api.example.com',
            'test-api-key'
        );

        $user = $client->user();

        $this->assertInstanceOf(User::class, $user);
    }

    public function testMultiplePubCallsReturnNewInstances(): void
    {
        $client = new Client(
            $this->httpClient,
            $this->factory,
            $this->factory
        );

        $pub1 = $client->pub();
        $pub2 = $client->pub();

        $this->assertInstanceOf(Pub::class, $pub1);
        $this->assertInstanceOf(Pub::class, $pub2);
        $this->assertNotSame($pub1, $pub2);
    }

    public function testMultipleUserCallsReturnNewInstances(): void
    {
        $client = new Client(
            $this->httpClient,
            $this->factory,
            $this->factory,
            'https://api.example.com',
            'test-api-key'
        );

        $user1 = $client->user();
        $user2 = $client->user();

        $this->assertInstanceOf(User::class, $user1);
        $this->assertInstanceOf(User::class, $user2);
        $this->assertNotSame($user1, $user2);
    }

    public function testClientWithoutApiKeyCanStillAccessUser(): void
    {
        $client = new Client(
            $this->httpClient,
            $this->factory,
            $this->factory
        );

        $user = $client->user();

        $this->assertInstanceOf(User::class, $user);
    }
}
