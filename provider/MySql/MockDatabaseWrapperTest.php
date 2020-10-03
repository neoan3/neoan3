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

}
