<?php

namespace Neoan3\Provider\MySql;

use PHPUnit\Framework\TestCase;


class TransformTest extends TestCase
{
    private array $mockStructure = [];
    private array $mockMock = [
        'id' => 'abc',
        'a_stamp' => '123456789',
        'a_stamp_st' => '123456789',
        'an_int' => 1,
        'a_string' => 'some',
        'a_boolean' => true,
        'a_datetime' => '2021-06-08 12:12:12',
        'a_datetime_st' => 123123123,
        'a_password' => 'pwd123',
        'a_decimal' => 2,
        'mock_sub' => [
            [
                'id' => 'cde',
                'mock_id' => 'abc',
                'a_stamp' => '123456789',
                'a_stamp_st' => '123456789',
                'an_int' => 1,
                'a_string' => 'some',
                'a_datetime' => '2021-06-08 12:12:12',
                'a_datetime_st' => 12312312313,
                'delete_date' => '123456',
                'delete_date_st' => '123456'
            ],
            [
                'id' => 'cde',
                'mock_id' => 'abc',
                'a_stamp' => '123456789',
                'a_stamp_st' => '123456789',
                'an_int' => 1,
                'a_string' => 'some',
                'delete_date' => '123456',
                'delete_date_st' => '123456'
            ]
        ]
    ];
    private DatabaseWrapper $dbMock;

    protected function setUp(): void
    {
        $this->mockStructure = json_decode(file_get_contents(__DIR__ . '/mockMigrate.json'), true);
        $this->dbMock = new MockDatabaseWrapper([], $this->mockStructure);

    }

    public function testUpdate()
    {
        $this->dbMock->mockUpdate('mock', $this->mockMock);

        $transform = new Transform('mock', $this->dbMock, $this->mockStructure);
        $actual = $transform->update($this->mockMock);
        $this->assertArrayHasKey('id', $actual);
        $this->assertArrayHasKey('mock_sub', $actual);
    }
    public function testUpdateTypeConditionsValidate()
    {

        $mockMock = $this->mockMock;
        // change datetime to now
        $mockMock['a_datetime'] = '.';
        // change another datetime to JS date
        $mockMock['mock_sub'][0]['a_datetime'] = '2021-06-12T12:30:00 GMT+0200 (CEST)';
        $this->dbMock->mockUpdate('mock', $this->mockMock);

        $transform = new Transform('mock', $this->dbMock, $this->mockStructure);
        $actual = $transform->update($mockMock);
        $this->assertArrayHasKey('id', $actual);


    }
    public function testUpdateAddedSub()
    {
        $mockMockCopy = $this->mockMock;
        unset($mockMockCopy['mock_sub'][0]['id']);

        foreach ($mockMockCopy as $potential => $values){
            if(is_array($values)){
                for($i = 0; $i < count($values); $i++){
                    $this->dbMock->registerResult('update');
                }
            }
        }
        $this->dbMock->registerResult('update main');
        // reattach id
        $this->dbMock->mockGet('mock', $this->mockMock);
        $transform = new Transform('mock', $this->dbMock, $this->mockStructure);
        $newSub = $transform->update($mockMockCopy);
        $this->assertArrayHasKey('id', $newSub);
        $this->assertArrayHasKey('mock_sub', $newSub);
    }

    public function testGet()
    {
        $this->dbMock->mockGet('mock');
        $transform = new Transform('mock', $this->dbMock, $this->mockStructure);
        $actual = $transform->get('123');
        $this->assertArrayHasKey('id', $actual);
        $this->assertArrayHasKey('mock_sub', $actual);
    }


    public function testFind()
    {
        $this->dbMock->registerResult([['id' => '123']]);
        $this->dbMock->mockGet('mock');
        $transform = new Transform('mock', $this->dbMock, $this->mockStructure);
        $actual = $transform->find(['mock_sub.mock_id' => '123456789']);
        $this->assertArrayHasKey('id', $actual[0]);
        $this->assertArrayHasKey('mock_sub', $actual[0]);
    }

    public function testFindException()
    {
        $transform = new Transform('mock', new DatabaseWrapper(), $this->mockStructure);
        $this->expectException(\Exception::class);
        $transform->find(['mock_unknown.mock_unknown' => '123456789']);
    }
    public function testFindShortHand()
    {
        $this->dbMock->registerResult([]);
        $this->dbMock->mockGet('mock');
        $transform = new Transform('mock', $this->dbMock, $this->mockStructure);
        $try = $transform->find(['^mock_sub.id']);
        $this->assertEmpty($try);
    }
    public function testFindShallow()
    {
        $this->dbMock->registerResult([['id' => '123']]);
        $this->dbMock->mockGet('mock');
        $transform = new Transform('mock', $this->dbMock, $this->mockStructure);
        $try = $transform->find(['an_int' => 1]);
        $this->assertIsArray($try);
    }
    public function testFindShallowFail()
    {
        $transform = new Transform('mock', $this->dbMock, $this->mockStructure);
        $this->expectException(\Exception::class);
        $try = $transform->find(['unknown' => 1]);
    }

    public function testDelete()
    {
        // soft
        $this->dbMock->mockDelete('mock');
        // hard
        $this->dbMock->mockDelete('mock');
        $transform = new Transform('mock', $this->dbMock, $this->mockStructure);
        $this->assertTrue($transform->delete('abc'));
        $this->assertTrue($transform->delete('abc', true));

    }
    public function testFormatResult()
    {
        $model = $this->dbMock->mockModel('mock');
        $sqlResult = ['id' => $model['id']];

        foreach ($model as $key => $value){
            if(is_array($value)){
                foreach ($value[0] as $subKey => $subValue){
                    $sqlResult[$key.'_'.$subKey] = $subValue;
                }
            } else {
                $sqlResult['mock_'.$key] = $value;
            }
        }
        $notDup = $sqlResult;
        $notDup['mock_sub_id'] = 'else';
        $this->dbMock->registerResult([$sqlResult, $sqlResult, $notDup]);
        $transform = new Transform('mock', $this->dbMock, $this->mockStructure);
        $actual = $transform->get('123');
        $this->assertArrayHasKey('id', $actual);
        $this->assertArrayHasKey('mock_sub', $actual);
    }

    public function testCreate()
    {
        $this->dbMock->mockModel('mock');
        $this->dbMock->registerResult([['id' => '123']]);
        $this->dbMock->mockUpdate('mock', $this->mockMock);
        $transform = new Transform('mock', $this->dbMock, $this->mockStructure);
        $actual = $transform->create($this->mockMock);
        $this->assertArrayHasKey('id', $actual);
        $this->assertArrayHasKey('mock_sub', $actual);
    }
}
