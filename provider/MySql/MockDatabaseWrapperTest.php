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
        $structure = [
            'mock' => [
                'id' => ['type' => 'binary(16)', 'key' => 'primary'],
                'a_stamp' => ['type' => 'timestamp', 'key' => null],
                'an_int' => ['type' => 'int(11)', 'key' => null],
                'a_string' => ['type' => 'varchar(255)', 'key' => null]
            ],
            'mock_sub' => [
                'id' => ['type' => 'binary(16)', 'key' => 'primary'],
                'mock_id' => ['type' => 'binary(16)', 'key' => null],
                'a_stamp' => ['type' => 'timestamp', 'key' => null],
                'an_int' => ['type' => 'int(11)', 'key' => null],
                'a_string' => ['type' => 'varchar(255)', 'key' => null]
            ]
        ];
        $db = new MockDatabaseWrapper([], $structure);
        $model = $db->mockModel('mock');
        $this->assertSame('123456789', $model['id']);
        $this->assertSame(1, $model['an_int']);
    }

}
