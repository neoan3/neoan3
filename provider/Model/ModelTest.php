<?php

namespace Neoan3\Provider\Model;

use Neoan3\Provider\MySql\DatabaseWrapper;
use Neoan3\Provider\MySql\MockDatabaseWrapper;
use PHPUnit\Framework\TestCase;

class MockClass implements ModelWrapper {
    use ModelWrapperTrait;
    private ?string $id = null;
    private ?string $name = null;
    static function create($fakeModel){
        $fakeModel['id'] = '123';
        return $fakeModel;
    }
    static function get($id)
    {
        if($id === 'fail'){
            return [];
        }
        return ['id'=> $id, 'name' => 'test'];
    }
    static function find($array, $any = []): array
    {
        if(isset($array['fail'])){
            return [];
        }
        return [['id'=> '123', 'name' => 'test']];
    }
    static function update($obj)
    {
        return $obj;
    }
    public function setName($in): self
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

        $test = MockClass::retrieveOne(['name'=>'test']);
        $this->assertArrayHasKey('id', $test->toArray());
        $test = MockClass::retrieveOne('123');
        $this->assertArrayHasKey('name', $test->toArray());

        $many = MockClass::retrieveMany(['name'=>'test']);
        $many->map(function($single){
            $single->rehydrate();
            $this->assertObjectHasAttribute('id', $single);
        });

    }
    public function testCollection()
    {
        $collection = MockClass::retrieveMany(['name'=>'test']);
        foreach ($collection as $iterator => $item){
            $this->assertObjectHasAttribute('id', $item);
        }
        $this->assertIsArray($collection->toArray());
        $this->assertIsInt($collection->count());
        $this->assertInstanceOf(Collection::class, $collection->store());
    }
    public function testHydrationFailed()
    {
        $f = new MockClass();
        $this->expectExceptionCode(404);
        $f->rehydrate();
    }
    public function testNothingFound()
    {
        $this->expectExceptionCode(404);
        MockClass::retrieveOne(['fail'=>'me']);
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