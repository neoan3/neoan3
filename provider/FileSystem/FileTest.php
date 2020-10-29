<?php

namespace Neoan3\Provider\FileSystem;

use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{

    public function testGlob()
    {
        $f = new File();
        $find = $f->glob(__DIR__ . '/*Test.php');
        $this->assertSame(__DIR__ . '/FileTest.php', $find[0]);
    }

    public function testGetContents()
    {
        $f = new File();
        $find = $f->getContents(__DIR__ . '/Native.php');
        $this->assertMatchesRegularExpression('/^<\?php/', $find);
    }

    public function testExists()
    {
        $f = new File();
        $find = $f->exists(__DIR__ . '/Native.php');
        $this->assertTrue($find);
    }

    public function testPutContents()
    {
        $f = new File();
        $path = __DIR__ . '/test.json';
        $f->putContents($path, 'text');
        $find = $f->getContents($path);
        $this->assertSame('text', $find);
        $f->delete($path);
    }
    public function testMockFileDelete()
    {
        $m = new MockFile();
        $m->putContents('mockPath/m.json','{}');
        $m->delete('mockPath/m.json');
        $this->assertFalse($m->exists('mockPath/m.json'));
    }
}
