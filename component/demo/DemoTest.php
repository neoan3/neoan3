<?php

namespace Neoan3\Components;

use PHPUnit\Framework\TestCase;

class DemoTest extends TestCase
{
    public function testInit()
    {
        $st = new Demo();
        $this->expectOutputRegex('/^<!doctype html>/');
        $st->init();
    }
}
