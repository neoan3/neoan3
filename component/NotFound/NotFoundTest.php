<?php

namespace Neoan3\Component\NotFound;

use PHPUnit\Framework\TestCase;

class NotFoundTest extends TestCase
{
    public function testInit()
    {
        $st = new NotFoundController();
        $this->expectOutputRegex('/^<!doctype html>/');
        $st->init();
    }
}
