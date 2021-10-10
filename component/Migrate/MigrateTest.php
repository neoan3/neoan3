<?php

namespace Neoan3\Component\Migrate;

use Neoan3\Provider\FileSystem\MockFile;
use Neoan3\Provider\FileSystem\Native;
use PHPUnit\Framework\TestCase;

// mock shell_exec
function shell_exec($input): string
{
    return 'some v1.5.2 trash';
}

/**
 * Class PostTest
 * Generated by neoan3-cli
 * @package Neoan3\Component\Migrate
 */
class MigrateTest extends TestCase
{
    private MigrateController $instance;
    private Native $fileSystem;
    function setUp(): void
    {
        $this->fileSystem = new MockFile();
        // safe-space!
        $this->fileSystem->putContents(dirname(path) . '/.safe-space','');
        $this->instance = new MigrateController(null, $this->fileSystem);
    }
        /**
     *  Route output shall have no errors
     */
    public function testInit()
    {

        $this->fileSystem->putContents(path.'/model/NotModel/migrate.json',"{}");
        $this->fileSystem->putContents(path.'/model/NotModel/migrate.json',"{}");
        $this->expectOutputRegex('/^<!doctype html>/');
        $this->instance->init();
    }

    public function testPutMigrate()
    {
        $this->fileSystem->putContents(path.'/model/NotModel/migrate.json',"{}");
        $res = $this->instance->putMigrate(['name'=>'tada']);
        $this->assertIsArray($res);
    }

    function testPostMigrate()
    {
        $testTable = [
            'not_model' => [
                'property' => [
                    'type' => 'varchar(200)',
                    'nullable' => true,
                    'key' => false
                ],
                'property2' => [
                    'type' => 'binary(16)',
                    'key' => 'primary',
                    'nullable' => false
                ],
                'property3' => [
                    'type' => 'datetime',
                    'key' => false,
                    'nullable' => true
                ],
                'property4' => [
                    'type' => 'text',
                    'key' => false,
                    'nullable' => true
                ]
            ]
        ];
        $testTable['not_model_sub'] = $testTable['not_model'];
        $mock =['migrate'=>$testTable,'name'=>'notModel', 'dbCredentials' => 'testing'];
        $this->fileSystem->putContents(path . '/model/NotModel','');
        $this->fileSystem->putContents(path.'/model/NotModel/migrate.json',"{}");
        $response = $this->instance->postMigrate($mock);
        $this->assertIsArray($response);
        // test fail
        $failMock = ['migrate'=>[],'name'=>'does_not_exist'];
        $response = $this->instance->postMigrate($failMock);
        $this->assertFalse($response['success']);
    }

    public function testNotSafeSpace(){
        $emptyMockFile = new MockFile();
        $newInstance = new MigrateController(null, $emptyMockFile);
        $this->expectExceptionCode(401);
        $newInstance->putMigrate(['name'=>'thing']);


    }

}
