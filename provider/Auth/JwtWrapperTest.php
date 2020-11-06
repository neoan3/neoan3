<?php

namespace Auth;

use Neoan3\Apps\Stateless;
use Neoan3\Core\RouteException;
use Neoan3\Provider\Auth\JwtWrapper;
use PHPUnit\Framework\TestCase;

class JwtWrapperTest extends TestCase
{
    /**
     * @var JwtWrapper
     */
    private JwtWrapper $jwtWrapper;

    protected function setUp(): void
    {
        $this->jwtWrapper = new JwtWrapper();

    }

    public function testAssignFail()
    {
        // no secret set
        $this->jwtWrapper->setSecret('');


        $this->expectException(RouteException::class);
        $this->jwtWrapper->assign('123', 'all');
    }
    public function testAssign()
    {
        $this->jwtWrapper->setSecret('my-secret');
        $working = $this->jwtWrapper->assign('123', 'all', ['any'=>'payload']);
        $this->assertMatchesRegularExpression('/[a-z0-9]+\.[a-z0-9]+/i', $working);
    }

    public function testRestrictFail()
    {
        $this->expectException(RouteException::class);
        $this->jwtWrapper->restrict(['superAdmin']);
    }
    public function testRestrict()
    {
        $this->jwtWrapper->setSecret('my-secret');
        Stateless::setAuthorization($this->jwtWrapper->assign('123', ['all']));
        $get = $this->jwtWrapper->restrict(['all']);
        $this->assertSame('123', $get->getUserId());
    }
    public function testValidateFail()
    {
        $this->expectException(RouteException::class);
        $this->jwtWrapper->validate('123.234.asd');
    }
    public function testValidate()
    {
        $this->jwtWrapper->setSecret('my-secret');
        $jwt = $this->jwtWrapper->assign('123', ['all']);
        $get = $this->jwtWrapper->validate($jwt->getToken());
        $this->assertSame('123', $get->getUserId());
    }
    public function testLogout()
    {
        $this->jwtWrapper->setSecret('my-secret');
        $jwt = $this->jwtWrapper->assign('123', ['all']);
        $this->assertFalse($this->jwtWrapper->logout());
    }
}
