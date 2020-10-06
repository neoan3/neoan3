<?php

namespace Neoan3\Components;

use PHPUnit\Framework\TestCase;

class EndpointTest extends TestCase
{
    private Endpoint $instance;
    function setUp(): void
    {
        $this->instance = new Endpoint();
    }

    function testGetEndpoint()
    {
        $response = $this->instance->getEndpoint(null,null,['some' => 'value']);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('some', $response);
        $this->assertSame('value', $response['some']);
    }
}
