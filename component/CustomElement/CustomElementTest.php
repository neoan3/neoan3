<?php

namespace Neoan3\Component\CustomElement;

use PHPUnit\Framework\TestCase;

class CustomElementTest extends TestCase
{
    private CustomElementController $instance;
    function setUp(): void
    {
        $this->instance = new CustomElementController();
    }
    
    function testGetCustomElement()
    {
        $response = $this->instance->getCustomElement();
        $this->assertIsArray($response);
        $this->assertArrayHasKey('tip', $response);
    }

}
