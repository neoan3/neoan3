<?php

namespace Neoan3\Components;

use PHPUnit\Framework\TestCase;

class NotFoundTest extends TestCase
{
    public function testInit()
    {
        $st = new NotFound();
        $this->expectOutputRegex('/^<!doctype html>/');
        $st->init();
    }
}
