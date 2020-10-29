<?php

namespace Neoan3\Provider\Auth;

use PHPUnit\Framework\TestCase;

class SessionWrapperTest extends TestCase
{
    /**
     * @var SessionWrapper
     */
    private SessionWrapper $instance;

    protected function setUp(): void
    {
        $this->instance = new SessionWrapper();
        $this->instance->setSecret('n3');
    }

    public function testAssign()
    {
        $authObj = $this->instance->assign('abc', ['read'],['some'=>'key']);
        $this->assertSame('abc', $authObj->getUserId());
        $this->assertSame('key', $authObj->getPayload()['some']);
    }


    public function testRestrict()
    {
        $this->instance->assign('abc', ['read']);
        $authObj = $this->instance->restrict(['read']);
        $this->assertSame('abc', $authObj->getUserId());
        $this->assertSame(['read'], $authObj->getScope());
    }

    public function testRestrictFail()
    {
        $this->instance->assign('abc', ['read']);
        $this->expectException(\Exception::class);
        $authObj = $this->instance->restrict(['superAdmin']);
    }

    public function testValidateFail()
    {
        $this->expectException(\Exception::class);
        $failInstance = new SessionWrapper('another');
        $failInstance->logout();
        $authObj = $failInstance->validate();
    }

}
