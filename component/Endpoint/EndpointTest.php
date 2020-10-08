<?php

namespace Neoan3\Component\Endpoint;

use PHPUnit\Framework\TestCase;

class EndpointTest extends TestCase
{
    private EndpointController $instance;
    function setUp(): void
    {
        $this->instance = new EndpointController();
    }

    function testGetEndpoint()
    {
        $response = $this->instance->getEndpoint(null,null,['some' => 'value']);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('some', $response);
        $this->assertSame('value', $response['some']);
    }
}
