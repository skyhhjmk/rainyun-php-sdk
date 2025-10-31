<?php

namespace RainYun\Tests\Unit\Endpoints;

use RainYun\Endpoints\Product\RcsGetOptions;
use RainYun\Tests\TestCase;

class RcsGetOptionsTest extends TestCase
{
    public function testMakeReturnsInstance(): void
    {
        $options = RcsGetOptions::make();
        
        $this->assertInstanceOf(RcsGetOptions::class, $options);
    }

    public function testDefaultValues(): void
    {
        $options = RcsGetOptions::make();
        $array = $options->toArray();
        
        $this->assertEquals('{}', $array['options']);
        $this->assertArrayNotHasKey('is_rgpu', $array);
    }

    public function testIsRgpuMethod(): void
    {
        $options = RcsGetOptions::make()->isRgpu(true);
        $array = $options->toArray();
        
        $this->assertTrue($array['is_rgpu']);
    }

    public function testIsRgpuFalse(): void
    {
        $options = RcsGetOptions::make()->isRgpu(false);
        $array = $options->toArray();
        
        $this->assertFalse($array['is_rgpu']);
    }

    public function testOptionsMethodWithArray(): void
    {
        $customOptions = ['filter' => 'value', 'page' => 1];
        $options = RcsGetOptions::make()->options($customOptions);
        $array = $options->toArray();
        
        $this->assertJson($array['options']);
        $decoded = json_decode($array['options'], true);
        $this->assertEquals($customOptions, $decoded);
    }

    public function testOptionsMethodWithString(): void
    {
        $jsonString = '{"custom":"value"}';
        $options = RcsGetOptions::make()->options($jsonString);
        $array = $options->toArray();
        
        $this->assertEquals($jsonString, $array['options']);
    }

    public function testChainableMethods(): void
    {
        $options = RcsGetOptions::make()
            ->isRgpu(true)
            ->options(['test' => 'data']);
        
        $array = $options->toArray();
        
        $this->assertTrue($array['is_rgpu']);
        $decoded = json_decode($array['options'], true);
        $this->assertEquals(['test' => 'data'], $decoded);
    }

    public function testToJsonMethod(): void
    {
        $options = RcsGetOptions::make()
            ->isRgpu(false)
            ->options(['filter' => 'test']);
        
        $json = $options->toJson();
        $decoded = json_decode($json, true);
        
        $this->assertJson($json);
        $this->assertFalse($decoded['is_rgpu']);
        $this->assertIsString($decoded['options']);
    }

    public function testToArrayWithoutIsRgpu(): void
    {
        $options = RcsGetOptions::make()->options(['test' => 'value']);
        $array = $options->toArray();
        
        $this->assertArrayNotHasKey('is_rgpu', $array);
        $this->assertArrayHasKey('options', $array);
    }

    public function testEmptyOptionsArray(): void
    {
        $options = RcsGetOptions::make()->options([]);
        $array = $options->toArray();
        
        $this->assertEquals('{}', $array['options']);
    }

    public function testComplexOptions(): void
    {
        $complexOptions = [
            'nested' => [
                'key' => 'value',
                'array' => [1, 2, 3]
            ],
            'boolean' => true,
            'number' => 42
        ];
        
        $options = RcsGetOptions::make()->options($complexOptions);
        $array = $options->toArray();
        
        $decoded = json_decode($array['options'], true);
        $this->assertEquals($complexOptions, $decoded);
    }

    public function testJsonEncodingWithUnicode(): void
    {
        $options = RcsGetOptions::make()->options(['message' => '你好世界']);
        $array = $options->toArray();
        
        $this->assertStringContainsString('你好世界', $array['options']);
    }
}
