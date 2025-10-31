<?php

namespace RainYun\Tests\Unit;

use RainYun\Options;
use RainYun\Tests\TestCase;

class OptionsTest extends TestCase
{
    public function testMakeReturnsInstance(): void
    {
        $options = Options::make();
        
        $this->assertInstanceOf(Options::class, $options);
    }

    public function testDefaultValues(): void
    {
        $options = Options::make();
        $array = $options->toArray();
        
        $this->assertEquals([], $array['sort']);
        $this->assertEquals(1, $array['page']);
        $this->assertEquals(20, $array['perPage']);
        $this->assertEquals([], $array['valueFilters']);
    }

    public function testSortMethod(): void
    {
        $sort = ['created_at' => 'desc', 'name' => 'asc'];
        $options = Options::make()->sort($sort);
        
        $this->assertEquals($sort, $options->toArray()['sort']);
    }

    public function testPageMethod(): void
    {
        $options = Options::make()->page(5);
        
        $this->assertEquals(5, $options->toArray()['page']);
    }

    public function testPerPageMethod(): void
    {
        $options = Options::make()->perPage(50);
        
        $this->assertEquals(50, $options->toArray()['perPage']);
    }

    public function testValueFiltersMethod(): void
    {
        $filters = ['status' => 'active', 'type' => 'premium'];
        $options = Options::make()->valueFilters($filters);
        
        $this->assertEquals($filters, $options->toArray()['valueFilters']);
    }

    public function testFilterMethod(): void
    {
        $options = Options::make()
            ->filter('status', 'active')
            ->filter('type', 'premium');
        
        $expected = ['status' => 'active', 'type' => 'premium'];
        $this->assertEquals($expected, $options->toArray()['valueFilters']);
    }

    public function testChainableMethods(): void
    {
        $options = Options::make()
            ->page(2)
            ->perPage(30)
            ->filter('status', 'active')
            ->sort(['created_at' => 'desc']);
        
        $array = $options->toArray();
        
        $this->assertEquals(2, $array['page']);
        $this->assertEquals(30, $array['perPage']);
        $this->assertEquals(['status' => 'active'], $array['valueFilters']);
        $this->assertEquals(['created_at' => 'desc'], $array['sort']);
    }

    public function testToJsonMethod(): void
    {
        $options = Options::make()
            ->page(1)
            ->perPage(10)
            ->filter('status', 'active');
        
        $json = $options->toJson();
        $decoded = json_decode($json, true);
        
        $this->assertJson($json);
        $this->assertEquals(1, $decoded['page']);
        $this->assertEquals(10, $decoded['perPage']);
        $this->assertEquals('active', $decoded['valueFilters']['status']);
    }

    public function testToArrayMethod(): void
    {
        $options = Options::make()
            ->page(3)
            ->perPage(25);
        
        $array = $options->toArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('sort', $array);
        $this->assertArrayHasKey('page', $array);
        $this->assertArrayHasKey('perPage', $array);
        $this->assertArrayHasKey('valueFilters', $array);
    }

    public function testMultipleFilters(): void
    {
        $options = Options::make()
            ->filter('category', 'tech')
            ->filter('status', 'published')
            ->filter('author', 'john');
        
        $filters = $options->toArray()['valueFilters'];
        
        $this->assertCount(3, $filters);
        $this->assertEquals('tech', $filters['category']);
        $this->assertEquals('published', $filters['status']);
        $this->assertEquals('john', $filters['author']);
    }

    public function testJsonEncodingFormat(): void
    {
        $options = Options::make()
            ->filter('url', 'https://example.com/path')
            ->filter('message', '你好世界');
        
        $json = $options->toJson();
        
        // Test JSON_UNESCAPED_SLASHES
        $this->assertStringContainsString('https://example.com/path', $json);
        
        // Test JSON_UNESCAPED_UNICODE
        $this->assertStringContainsString('你好世界', $json);
    }

    public function testOverwriteFilter(): void
    {
        $options = Options::make()
            ->filter('status', 'draft')
            ->filter('status', 'published');
        
        $filters = $options->toArray()['valueFilters'];
        
        $this->assertEquals('published', $filters['status']);
    }

    public function testEmptySort(): void
    {
        $options = Options::make()->sort([]);
        
        $this->assertEquals([], $options->toArray()['sort']);
    }

    public function testComplexSortAndFilters(): void
    {
        $options = Options::make()
            ->sort(['created_at' => 'desc', 'name' => 'asc', 'priority' => 'desc'])
            ->page(10)
            ->perPage(100)
            ->filter('tag', 'important')
            ->filter('archived', false);
        
        $array = $options->toArray();
        
        $this->assertCount(3, $array['sort']);
        $this->assertEquals(10, $array['page']);
        $this->assertEquals(100, $array['perPage']);
        $this->assertCount(2, $array['valueFilters']);
    }
}
