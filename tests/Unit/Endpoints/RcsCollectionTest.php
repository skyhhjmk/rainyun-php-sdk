<?php

namespace RainYun\Tests\Unit\Endpoints;

use RainYun\Endpoints\Product\Collection\RcsCollection;
use RainYun\Tests\TestCase;

class RcsCollectionTest extends TestCase
{
    private function getSampleData(): array
    {
        return [
            'code' => 200,
            'data' => [
                'TotalRecords' => 2,
                'Records' => [
                    [
                        'ID' => 220195,
                        'HostName' => 'RainYun-FYb0aZxi',
                        'Status' => 'running',
                        'MainIPv4' => '114.66.58.128',
                        'CPU' => 4,
                        'Memory' => 8192,
                        'Disk' => 30,
                        'Node' => [
                            'Region' => 'cn-nb1',
                            'ChineseName' => '宁波云服务器节点28'
                        ]
                    ],
                    [
                        'ID' => 212700,
                        'HostName' => 'RainYun-tEaDSHDq',
                        'Status' => 'running',
                        'MainIPv4' => '75.127.89.106',
                        'CPU' => 2,
                        'Memory' => 4096,
                        'Disk' => 40,
                        'Node' => [
                            'Region' => 'cn-hk4',
                            'ChineseName' => '香港4区云服务器节点11'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function testIsSuccessReturnsTrueForCode200(): void
    {
        $collection = new RcsCollection(['code' => 200]);
        
        $this->assertTrue($collection->isSuccess());
    }

    public function testIsSuccessReturnsFalseForNon200Code(): void
    {
        $collection = new RcsCollection(['code' => 404]);
        
        $this->assertFalse($collection->isSuccess());
    }

    public function testGetCodeReturnsCode(): void
    {
        $collection = new RcsCollection(['code' => 200]);
        
        $this->assertEquals(200, $collection->getCode());
    }

    public function testGetDataReturnsData(): void
    {
        $data = $this->getSampleData();
        $collection = new RcsCollection($data);
        
        $result = $collection->getData();
        $this->assertNotNull($result);
        $this->assertEquals(2, $result->TotalRecords);
    }

    public function testGetRecordsReturnsArray(): void
    {
        $data = $this->getSampleData();
        $collection = new RcsCollection($data);
        
        $records = $collection->getRecords();
        $this->assertIsArray($records);
        $this->assertCount(2, $records);
    }

    public function testGetTotalRecordsReturnsCount(): void
    {
        $data = $this->getSampleData();
        $collection = new RcsCollection($data);
        
        $this->assertEquals(2, $collection->getTotalRecords());
    }

    public function testGetTotalRecordsReturnsZeroWhenMissing(): void
    {
        $collection = new RcsCollection(['code' => 200]);
        
        $this->assertEquals(0, $collection->getTotalRecords());
    }

    public function testGetByStatusFiltersCorrectly(): void
    {
        $data = $this->getSampleData();
        $data['data']['Records'][] = [
            'ID' => 300000,
            'HostName' => 'stopped-instance',
            'Status' => 'stopped',
            'Node' => ['Region' => 'cn-sh1']
        ];
        
        $collection = new RcsCollection($data);
        
        $running = $collection->getByStatus('running');
        $this->assertCount(2, $running);
        
        $stopped = $collection->getByStatus('stopped');
        $this->assertCount(1, $stopped);
    }

    public function testGetByStatusReturnsEmptyWhenNoMatch(): void
    {
        $data = $this->getSampleData();
        $collection = new RcsCollection($data);
        
        $result = $collection->getByStatus('nonexistent');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetByRegionFiltersCorrectly(): void
    {
        $data = $this->getSampleData();
        $collection = new RcsCollection($data);
        
        $nb1Instances = $collection->getByRegion('cn-nb1');
        $this->assertCount(1, $nb1Instances);
        
        $hk4Instances = $collection->getByRegion('cn-hk4');
        $this->assertCount(1, $hk4Instances);
    }

    public function testGetByRegionReturnsEmptyWhenNoMatch(): void
    {
        $data = $this->getSampleData();
        $collection = new RcsCollection($data);
        
        $result = $collection->getByRegion('cn-unknown');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetByIdReturnsCorrectInstance(): void
    {
        $data = $this->getSampleData();
        $collection = new RcsCollection($data);
        
        $instance = $collection->getById(220195);
        $this->assertNotNull($instance);
        $this->assertEquals('RainYun-FYb0aZxi', $instance->HostName);
    }

    public function testGetByIdReturnsNullWhenNotFound(): void
    {
        $data = $this->getSampleData();
        $collection = new RcsCollection($data);
        
        $instance = $collection->getById(999999);
        $this->assertNull($instance);
    }

    public function testCountReturnsNumberOfRecords(): void
    {
        $data = $this->getSampleData();
        $collection = new RcsCollection($data);
        
        $this->assertEquals(2, $collection->count());
    }

    public function testCountReturnsZeroWhenNoRecords(): void
    {
        $collection = new RcsCollection(['code' => 200, 'data' => ['TotalRecords' => 0]]);
        
        $this->assertEquals(0, $collection->count());
    }

    public function testMagicAccessToProperties(): void
    {
        $data = $this->getSampleData();
        $collection = new RcsCollection($data);
        
        $this->assertEquals(200, $collection->code);
        $this->assertNotNull($collection->data);
        $this->assertEquals(2, $collection->data->TotalRecords);
    }

    public function testCompleteDataStructure(): void
    {
        $data = $this->getSampleData();
        $collection = new RcsCollection($data);
        
        $this->assertTrue($collection->isSuccess());
        $this->assertEquals(200, $collection->getCode());
        $this->assertEquals(2, $collection->getTotalRecords());
        
        $records = $collection->getRecords();
        $this->assertCount(2, $records);
        
        $first = $records[0];
        $this->assertEquals(220195, $first->ID);
        $this->assertEquals('RainYun-FYb0aZxi', $first->HostName);
        $this->assertEquals('running', $first->Status);
        $this->assertEquals('114.66.58.128', $first->MainIPv4);
        $this->assertEquals(4, $first->CPU);
        $this->assertEquals(8192, $first->Memory);
        $this->assertEquals(30, $first->Disk);
        $this->assertEquals('cn-nb1', $first->Node->Region);
    }

    public function testGetByStatusHandlesMissingData(): void
    {
        $collection = new RcsCollection(['code' => 200]);
        
        $result = $collection->getByStatus('running');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetByRegionHandlesMissingData(): void
    {
        $collection = new RcsCollection(['code' => 200]);
        
        $result = $collection->getByRegion('cn-nb1');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
