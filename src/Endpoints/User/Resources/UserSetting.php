<?php

namespace RainYun\Endpoints\User\Resources;

use Psr\Http\Client\ClientExceptionInterface;
use RainYun\Endpoints\AbstractResource;
use RainYun\Endpoints\User\UserCollection;

/**
 * UserSetting API resource.
 *
 * Provides methods to interact with the /user/ endpoint for updating user settings.
 */
class UserSetting extends AbstractResource
{
    /**
     * Update user data.
     *
     * Requires API key authentication via x-api-key header.
     *
     * Example:
     * ```php
     * $result = $client->user()->setting()->update('name', 'New Name');
     * ```
     *
     * @param string $option Setting option to update (name, icon, alipay_account, alipay_name, password, email, phone, apikey, totp, login_tfa, unbind_social_media)
     * @param string $value New value for the setting
     * @return UserCollection Response collection containing update result
     * @throws ClientExceptionInterface
     */
    public function update(string $option, string $value): UserCollection
    {
        $uri = $this->buildUri('/user/');
        
        $body = json_encode([
            'option' => $option,
            'value' => $value
        ]);
        
        $request = $this->requestFactory->createRequest('PATCH', $uri)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('x-api-key', $this->apiKey)
            ->withBody($this->createStream($body));
        
        $response = $this->httpClient->sendRequest($request);
        
        return parent::decodeResponse($response, UserCollection::class);
    }

    /**
     * Create a stream from string content.
     *
     * @param string $content Content to create stream from
     * @return \Psr\Http\Message\StreamInterface
     */
    private function createStream(string $content)
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $content);
        rewind($stream);
        
        return new class($stream) implements \Psr\Http\Message\StreamInterface {
            private $stream;
            
            public function __construct($stream)
            {
                $this->stream = $stream;
            }
            
            public function __toString(): string
            {
                return $this->getContents();
            }
            
            public function close(): void
            {
                if (is_resource($this->stream)) {
                    fclose($this->stream);
                }
            }
            
            public function detach()
            {
                $result = $this->stream;
                $this->stream = null;
                return $result;
            }
            
            public function getSize(): ?int
            {
                if (!is_resource($this->stream)) {
                    return null;
                }
                $stats = fstat($this->stream);
                return $stats['size'] ?? null;
            }
            
            public function tell(): int
            {
                if (!is_resource($this->stream)) {
                    throw new \RuntimeException('Stream is detached');
                }
                $position = ftell($this->stream);
                if ($position === false) {
                    throw new \RuntimeException('Unable to determine stream position');
                }
                return $position;
            }
            
            public function eof(): bool
            {
                return !is_resource($this->stream) || feof($this->stream);
            }
            
            public function isSeekable(): bool
            {
                if (!is_resource($this->stream)) {
                    return false;
                }
                $meta = stream_get_meta_data($this->stream);
                return $meta['seekable'];
            }
            
            public function seek(int $offset, int $whence = SEEK_SET): void
            {
                if (!is_resource($this->stream)) {
                    throw new \RuntimeException('Stream is detached');
                }
                if (fseek($this->stream, $offset, $whence) === -1) {
                    throw new \RuntimeException('Unable to seek to stream position');
                }
            }
            
            public function rewind(): void
            {
                $this->seek(0);
            }
            
            public function isWritable(): bool
            {
                if (!is_resource($this->stream)) {
                    return false;
                }
                $meta = stream_get_meta_data($this->stream);
                $mode = $meta['mode'];
                return strpos($mode, 'w') !== false || strpos($mode, '+') !== false;
            }
            
            public function write(string $string): int
            {
                if (!is_resource($this->stream)) {
                    throw new \RuntimeException('Stream is detached');
                }
                $result = fwrite($this->stream, $string);
                if ($result === false) {
                    throw new \RuntimeException('Unable to write to stream');
                }
                return $result;
            }
            
            public function isReadable(): bool
            {
                if (!is_resource($this->stream)) {
                    return false;
                }
                $meta = stream_get_meta_data($this->stream);
                $mode = $meta['mode'];
                return strpos($mode, 'r') !== false || strpos($mode, '+') !== false;
            }
            
            public function read(int $length): string
            {
                if (!is_resource($this->stream)) {
                    throw new \RuntimeException('Stream is detached');
                }
                $result = fread($this->stream, $length);
                if ($result === false) {
                    throw new \RuntimeException('Unable to read from stream');
                }
                return $result;
            }
            
            public function getContents(): string
            {
                if (!is_resource($this->stream)) {
                    throw new \RuntimeException('Stream is detached');
                }
                $result = stream_get_contents($this->stream);
                if ($result === false) {
                    throw new \RuntimeException('Unable to read stream contents');
                }
                return $result;
            }
            
            public function getMetadata(?string $key = null)
            {
                if (!is_resource($this->stream)) {
                    return $key ? null : [];
                }
                $meta = stream_get_meta_data($this->stream);
                if ($key === null) {
                    return $meta;
                }
                return $meta[$key] ?? null;
            }
        };
    }
}
