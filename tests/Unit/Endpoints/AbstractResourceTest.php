<?php

namespace RainYun\Tests\Unit\Endpoints;

use RainYun\Endpoints\AbstractResource;
use RainYun\Collection;
use RainYun\Tests\TestCase;
use RainYun\Tests\Helpers\MockHttpClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;

class AbstractResourceTest extends TestCase
{
    private MockHttpClient $httpClient;
    private HttpFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new MockHttpClient();
        $this->factory = new HttpFactory();
    }

    public function testConstructorInitializesProperties(): void
    {
        $resource = $this->getMockForAbstractClass(
            AbstractResource::class,
            [
                $this->httpClient,
                $this->factory,
                $this->factory,
                'https://api.example.com',
                'test-api-key'
            ]
        );

        $this->assertInstanceOf(AbstractResource::class, $resource);
    }

    public function testBuildUriWithoutQueryParams(): void
    {
        $resource = $this->getMockForAbstractClass(
            AbstractResource::class,
            [
                $this->httpClient,
                $this->factory,
                $this->factory,
                'https://api.example.com'
            ]
        );

        $uri = $this->invokeMethod($resource, 'buildUri', ['/test/endpoint']);

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('api.example.com', $uri->getHost());
        $this->assertEquals('/test/endpoint', $uri->getPath());
        $this->assertEmpty($uri->getQuery());
    }

    public function testBuildUriWithQueryParams(): void
    {
        $resource = $this->getMockForAbstractClass(
            AbstractResource::class,
            [
                $this->httpClient,
                $this->factory,
                $this->factory,
                'https://api.example.com'
            ]
        );

        $queryParams = ['page' => 1, 'limit' => 10];
        $uri = $this->invokeMethod($resource, 'buildUri', ['/test', $queryParams]);

        $this->assertStringContainsString('page=1', $uri->getQuery());
        $this->assertStringContainsString('limit=10', $uri->getQuery());
    }

    public function testCreateGetRequestWithoutAuth(): void
    {
        $resource = $this->getMockForAbstractClass(
            AbstractResource::class,
            [
                $this->httpClient,
                $this->factory,
                $this->factory,
                'https://api.example.com'
            ]
        );

        $uri = $this->factory->createUri('https://api.example.com/test');
        $request = $this->invokeMethod($resource, 'createGetRequest', [$uri, false]);

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('application/json', $request->getHeaderLine('Accept'));
        $this->assertEquals('rainyun-php-sdk/0.1', $request->getHeaderLine('User-Agent'));
        $this->assertEmpty($request->getHeaderLine('x-api-key'));
    }

    public function testCreateGetRequestWithAuth(): void
    {
        $resource = $this->getMockForAbstractClass(
            AbstractResource::class,
            [
                $this->httpClient,
                $this->factory,
                $this->factory,
                'https://api.example.com',
                'test-api-key'
            ]
        );

        $uri = $this->factory->createUri('https://api.example.com/test');
        $request = $this->invokeMethod($resource, 'createGetRequest', [$uri, true]);

        $this->assertEquals('test-api-key', $request->getHeaderLine('x-api-key'));
    }

    public function testDecodeResponseWithJson(): void
    {
        $resource = $this->getMockForAbstractClass(
            AbstractResource::class,
            [
                $this->httpClient,
                $this->factory,
                $this->factory,
                'https://api.example.com'
            ]
        );

        $responseData = ['code' => 200, 'data' => ['test' => 'value']];
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($responseData)
        );

        $result = $this->invokeMethod($resource, 'decodeResponse', [$response, Collection::class]);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(200, $result->code);
        $this->assertEquals('value', $result->data->test);
    }

    public function testDecodeResponseWithNonJson(): void
    {
        $resource = $this->getMockForAbstractClass(
            AbstractResource::class,
            [
                $this->httpClient,
                $this->factory,
                $this->factory,
                'https://api.example.com'
            ]
        );

        $response = new Response(
            200,
            ['Content-Type' => 'text/plain'],
            'Plain text response'
        );

        $result = $this->invokeMethod($resource, 'decodeResponse', [$response, Collection::class]);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals('Plain text response', $result->raw);
        $this->assertEquals('text/plain', $result->content_type);
        $this->assertEquals(200, $result->status);
    }

    public function testConstructorTrimsTrailingSlash(): void
    {
        $resource = $this->getMockForAbstractClass(
            AbstractResource::class,
            [
                $this->httpClient,
                $this->factory,
                $this->factory,
                'https://api.example.com/'
            ]
        );

        $uri = $this->invokeMethod($resource, 'buildUri', ['/test']);
        
        // The path should be /test, not //test
        $this->assertEquals('/test', $uri->getPath());
    }

    /**
     * Helper method to invoke protected/private methods for testing
     */
    private function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
