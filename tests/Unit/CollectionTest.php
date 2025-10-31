<?php

namespace RainYun\Tests\Unit;

use RainYun\Collection;
use RainYun\Tests\TestCase;

class CollectionTest extends TestCase
{
    public function testConstructorWithEmptyArray(): void
    {
        $collection = new Collection([]);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(0, $collection->count());
    }

    public function testConstructorWithSimpleArray(): void
    {
        $data = ['name' => 'John', 'age' => 30];
        $collection = new Collection($data);
        
        $this->assertEquals('John', $collection->name);
        $this->assertEquals(30, $collection->age);
    }

    public function testConstructorWithNestedArray(): void
    {
        $data = [
            'user' => [
                'name' => 'John',
                'email' => 'john@example.com'
            ],
            'status' => 'active'
        ];
        
        $collection = new Collection($data);
        
        $this->assertInstanceOf(Collection::class, $collection->user);
        $this->assertEquals('John', $collection->user->name);
        $this->assertEquals('john@example.com', $collection->user->email);
        $this->assertEquals('active', $collection->status);
    }

    public function testConstructorWithArrayList(): void
    {
        $data = [
            'items' => [
                ['id' => 1, 'name' => 'Item 1'],
                ['id' => 2, 'name' => 'Item 2']
            ]
        ];
        
        $collection = new Collection($data);
        
        $this->assertIsArray($collection->items);
        $this->assertCount(2, $collection->items);
        $this->assertInstanceOf(Collection::class, $collection->items[0]);
        $this->assertEquals(1, $collection->items[0]->id);
        $this->assertEquals('Item 2', $collection->items[1]->name);
    }

    public function testMagicGet(): void
    {
        $collection = new Collection(['key' => 'value']);
        
        $this->assertEquals('value', $collection->key);
        $this->assertNull($collection->nonexistent);
    }

    public function testMagicIsset(): void
    {
        $collection = new Collection(['key' => 'value']);
        
        $this->assertTrue(isset($collection->key));
        $this->assertFalse(isset($collection->nonexistent));
    }

    public function testToArray(): void
    {
        $data = [
            'user' => [
                'name' => 'John',
                'age' => 30
            ],
            'items' => [
                ['id' => 1],
                ['id' => 2]
            ]
        ];
        
        $collection = new Collection($data);
        $array = $collection->toArray();
        
        $this->assertIsArray($array);
        $this->assertEquals($data, $array);
    }

    public function testToString(): void
    {
        $data = ['name' => 'John', 'age' => 30];
        $collection = new Collection($data);
        
        $json = (string) $collection;
        $decoded = json_decode($json, true);
        
        $this->assertJson($json);
        $this->assertEquals($data, $decoded);
    }

    public function testIteratorAggregate(): void
    {
        $data = ['a' => 1, 'b' => 2, 'c' => 3];
        $collection = new Collection($data);
        
        $result = [];
        foreach ($collection as $key => $value) {
            $result[$key] = $value;
        }
        
        $this->assertEquals($data, $result);
    }

    public function testCountable(): void
    {
        $collection = new Collection(['a' => 1, 'b' => 2, 'c' => 3]);
        
        $this->assertEquals(3, $collection->count());
        $this->assertCount(3, $collection);
    }

    public function testEmptyCollectionCount(): void
    {
        $collection = new Collection([]);
        
        $this->assertEquals(0, $collection->count());
    }

    public function testNestedCollectionUnwrap(): void
    {
        $data = [
            'level1' => [
                'level2' => [
                    'level3' => 'deep value'
                ]
            ]
        ];
        
        $collection = new Collection($data);
        $array = $collection->toArray();
        
        $this->assertEquals($data, $array);
    }

    public function testMixedTypeValues(): void
    {
        $data = [
            'string' => 'text',
            'int' => 42,
            'float' => 3.14,
            'bool' => true,
            'null' => null,
            'array' => ['item1', 'item2']
        ];
        
        $collection = new Collection($data);
        
        $this->assertEquals('text', $collection->string);
        $this->assertEquals(42, $collection->int);
        $this->assertEquals(3.14, $collection->float);
        $this->assertTrue($collection->bool);
        $this->assertNull($collection->null);
        $this->assertEquals(['item1', 'item2'], $collection->array);
    }

    public function testJsonEncodingWithUnicode(): void
    {
        $data = ['message' => '你好世界', 'emoji' => '😀'];
        $collection = new Collection($data);
        
        $json = (string) $collection;
        
        $this->assertStringContainsString('你好世界', $json);
        $this->assertStringContainsString('😀', $json);
    }
}
