<?php

namespace RainYun\Tests\Unit\Endpoints;

use RainYun\Endpoints\Pub\Collection\NewsCollection;
use RainYun\Tests\TestCase;

class NewsCollectionTest extends TestCase
{
    public function testIsSuccessReturnsTrueForCode200(): void
    {
        $collection = new NewsCollection(['code' => 200, 'data' => []]);
        
        $this->assertTrue($collection->isSuccess());
    }

    public function testIsSuccessReturnsFalseForNon200Code(): void
    {
        $collection = new NewsCollection(['code' => 404, 'data' => []]);
        
        $this->assertFalse($collection->isSuccess());
    }

    public function testIsSuccessReturnsFalseWhenCodeMissing(): void
    {
        $collection = new NewsCollection(['data' => []]);
        
        $this->assertFalse($collection->isSuccess());
    }

    public function testGetCodeReturnsCode(): void
    {
        $collection = new NewsCollection(['code' => 200]);
        
        $this->assertEquals(200, $collection->getCode());
    }

    public function testGetCodeReturnsNullWhenMissing(): void
    {
        $collection = new NewsCollection([]);
        
        $this->assertNull($collection->getCode());
    }

    public function testGetDataReturnsDataArray(): void
    {
        $data = [
            ['Type' => '更新动态', 'Title' => 'News 1'],
            ['Type' => '最新活动', 'Title' => 'News 2']
        ];
        
        $collection = new NewsCollection(['code' => 200, 'data' => $data]);
        
        $result = $collection->getData();
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testGetDataReturnsNullWhenMissing(): void
    {
        $collection = new NewsCollection(['code' => 200]);
        
        $this->assertNull($collection->getData());
    }

    public function testGetByTypeFiltersNewsByType(): void
    {
        $data = [
            ['Type' => '更新动态', 'Title' => 'Update 1'],
            ['Type' => '最新活动', 'Title' => 'Activity 1'],
            ['Type' => '更新动态', 'Title' => 'Update 2']
        ];
        
        $collection = new NewsCollection(['code' => 200, 'data' => $data]);
        
        $updates = $collection->getByType('更新动态');
        
        $this->assertCount(2, $updates);
    }

    public function testGetByTypeReturnsEmptyArrayWhenNoMatch(): void
    {
        $data = [
            ['Type' => '更新动态', 'Title' => 'Update 1']
        ];
        
        $collection = new NewsCollection(['code' => 200, 'data' => $data]);
        
        $result = $collection->getByType('不存在的类型');
        
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetByTypeReturnsEmptyArrayWhenDataMissing(): void
    {
        $collection = new NewsCollection(['code' => 200]);
        
        $result = $collection->getByType('更新动态');
        
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testCountReturnsNumberOfItems(): void
    {
        $data = [
            ['Type' => '更新动态', 'Title' => 'News 1'],
            ['Type' => '最新活动', 'Title' => 'News 2'],
            ['Type' => '更新动态', 'Title' => 'News 3']
        ];
        
        $collection = new NewsCollection(['code' => 200, 'data' => $data]);
        
        $this->assertEquals(3, $collection->count());
    }

    public function testCountReturnsZeroWhenDataMissing(): void
    {
        $collection = new NewsCollection(['code' => 200]);
        
        $this->assertEquals(0, $collection->count());
    }

    public function testCountReturnsZeroForEmptyData(): void
    {
        $collection = new NewsCollection(['code' => 200, 'data' => []]);
        
        $this->assertEquals(0, $collection->count());
    }

    public function testMagicGetAccessToProperties(): void
    {
        $collection = new NewsCollection(['code' => 200, 'message' => 'Success']);
        
        $this->assertEquals(200, $collection->code);
        $this->assertEquals('Success', $collection->message);
    }

    public function testCompleteNewsStructure(): void
    {
        $data = [
            [
                'Type' => '更新动态',
                'Title' => '系统升级通知',
                'TimeStamp' => '2024-01-15 10:00:00',
                'URL' => 'https://example.com/news/1'
            ]
        ];
        
        $collection = new NewsCollection(['code' => 200, 'data' => $data]);
        
        $this->assertTrue($collection->isSuccess());
        $this->assertEquals(200, $collection->getCode());
        
        $newsData = $collection->getData();
        $this->assertCount(1, $newsData);
        $this->assertEquals('更新动态', $newsData[0]->Type);
        $this->assertEquals('系统升级通知', $newsData[0]->Title);
    }
}
