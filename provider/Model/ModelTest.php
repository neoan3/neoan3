<?php

namespace Neoan3\Provider\Model;

use Neoan3\Provider\MySql\DatabaseWrapper;
use Neoan3\Provider\MySql\MockDatabaseWrapper;
use PHPUnit\Framework\TestCase;

class MockClass{
    use ModelWrapperTrait;
    private ?string $id = null;
    private ?string $name = null;
    static function create($fakeModel){
        $fakeModel['id'] = '123';
        return $fakeModel;
    }
    static function get($id)
    {
        return ['id'=> '123', 'name' => 'test'];
    }
    static function find($array)
    {
        return [['id'=> '123', 'name' => 'test']];
    }
    public function setName($in): static
    {
        $this->name = $in;
        return $this;
    }
}

class ModelTest extends TestCase
{
    use InitProvider;
    private static \Neoan3\Provider\MySql\DatabaseWrapper $db;
    public function testInit()
    {
        $providers = ['db' => new MockDatabaseWrapper([])];
        self::init($providers);
        $this->assertInstanceOf(DatabaseWrapper::class, self::$db);
    }
    public function testModelWrapperTrait()
    {
        $mock = new MockClass(['id'=> '123', 'name' => 'test']);
        $mock->setName('neoan');
        $mock->store('create');
        $this->assertArrayHasKey('id', $mock->toArray());



        $test = MockClass::retrieve(['name'=>'test']);
        $this->assertArrayHasKey('id', $test->toArray());
        $test = MockClass::retrieve('123');
        $this->assertArrayHasKey('name', $test->toArray());
    }
    public function testFailedTransaction()
    {
        $mock = new MockClass();
        $this->expectExceptionCode(500);
        $mock->store('pi');
    }

    public function testAttribute()
    {
        $attr = new InitModel(self::class);
        $attr->execute(['db' => new MockDatabaseWrapper([])]);
        $this->assertIsString($attr->modelClass,'attribute failed');
    }

}