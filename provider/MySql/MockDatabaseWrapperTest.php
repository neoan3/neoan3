<?php

namespace Neoan3\Provider\MySql;

use PHPUnit\Framework\TestCase;

class MockDatabaseWrapperTest extends TestCase
{

    public function testRegisterResult()
    {
        $db = new MockDatabaseWrapper();
        $test = ['some' => 'result'];
        $db->registerResult($test);
        $this->assertSame($test,$db->easy('hm'));
    }

    public function testSmart()
    {
        $db = new MockDatabaseWrapper();
        $test = ['some' => 'result'];
        $db->registerResult($test);
        $this->assertSame($test,$db->smart('hm'));
    }

    public function testMockModel()
    {
        $structure = json_decode(file_get_contents(__DIR__ . '/mockMigrate.json'),true);
        $db = new MockDatabaseWrapper([], $structure);
        $model = $db->mockModel('mock');
        $this->assertSame('123456789', $model['id']);
        $this->assertSame(1, $model['an_int']);
    }
    public function testMockFind()
    {
        $structure = json_decode(file_get_contents(__DIR__ . '/mockMigrate.json'),true);
        $db = new MockDatabaseWrapper([], $structure);
        $model = $db->mockFind('mock');
        $this->assertSame('123456789', $model['id']);
    }
    public function testMockUpdate()
    {
        $structure = json_decode(file_get_contents(__DIR__ . '/mockMigrate.json'),true);
        $db = new MockDatabaseWrapper([], $structure);
        $modelExpected = $db->mockModel('mock');
        $model = $db->mockUpdate('mock', $modelExpected);
        $this->assertSame($modelExpected['id'], $model['id']);
    }

}
