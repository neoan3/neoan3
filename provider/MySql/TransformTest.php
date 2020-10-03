<?php

namespace Neoan3\Provider\MySql;

use PHPUnit\Framework\TestCase;

function file_get_contents($ins)
{
    return '{"mock":{"id":{"type": "binary(16)", "key":"primary"}},"mock_sub":{"id":{"type": "binary(16)", "key":"primary"},{"mock_id":{"type": "binary(16)", "key":false}}}';
}

class TransformTest extends TestCase
{
    private array $mockStructure = [
        'mock' => ['id' => ['type' => 'binary(16)', 'key' => 'primary']],
        'mock_sub' => ['id' => ['type' => 'binary(16)', 'key' => 'primary'],'mock_id' => ['type' => 'binary(16)', 'key' => false]]
    ];
    private array $mockMock = [
        'id' => 'abc',
        'mock_sub' => [
            ['id' => 'cde', 'mock_id' => 'abc']
        ]
    ];
    private DatabaseWrapper $dbMock;
    protected function setUp(): void
    {
        $this->dbMock = new MockDatabaseWrapper([], $this->mockStructure);
    }

    public function testUpdate()
    {
        $this->dbMock->mockUpdate('mock', $this->mockMock);
        $transform = new Transform('mock', $this->dbMock, $this->mockStructure);
        $actual = $transform->update($this->mockMock);
        $this->assertArrayHasKey('id', $actual);
        $this->assertArrayHasKey('mock_sub', $actual);
    }

    public function testGet()
    {
        $this->dbMock->mockGet('mock');
        $transform = new Transform('mock', $this->dbMock, $this->mockStructure);
        $actual = $transform->get('123');
        $this->assertArrayHasKey('id', $actual);
        $this->assertArrayHasKey('mock_sub', $actual);
    }

    public function testFind()
    {
        $this->dbMock->registerResult([['id' => '123']]);
        $this->dbMock->mockModel('mock');
        $this->dbMock->mockGet('mock');
        $transform = new Transform('mock', $this->dbMock, $this->mockStructure);
        $actual = $transform->find(['any'=>'thing']);
        $this->assertArrayHasKey('id', $actual[0]);
        $this->assertArrayHasKey('mock_sub', $actual[0]);
    }

    public function testCreate()
    {
        $this->dbMock->mockModel('mock');
        $this->dbMock->registerResult([['id' => '123']]);
        $this->dbMock->mockUpdate('mock', $this->mockMock);
        $transform = new Transform('mock', $this->dbMock, $this->mockStructure);
        $actual = $transform->create($this->mockMock);
        $this->assertArrayHasKey('id', $actual);
        $this->assertArrayHasKey('mock_sub', $actual);
    }
}
