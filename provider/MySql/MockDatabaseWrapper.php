<?php


namespace Neoan3\Provider\MySql;


class MockDatabaseWrapper extends DatabaseWrapper
{
    private array $results = [];
    private int $nextStep = 0;

    function registerResult($any)
    {
        $this->results[] = $any;
    }

    function easy($selectString, $conditions = [], $callFunctions = [])
    {
        $result = $this->results[$this->nextStep];
        $this->nextStep++;
        return $result;
    }

    function mockModel($model)
    {
        $transform = new Transform($model, $this);
        $random = [];
        foreach ($transform->modelStructure as $table => $fields) {
            foreach ($transform->modelStructure[$table] as $field => $specs) {
                switch (preg_replace('/[^a-z]/i', '', $specs['type'])) {
                    case 'binary':
                        $val = '123456789';
                        if ($table == $model) {
                            $random[$field] = $val;
                        } else {
                            $random[$table][$field] = $val;
                        }
                        break;
                    case 'timestamp':
                    case 'datetime':
                        $now = time();
                        $val = date('Y-m-d H:i:s', $now);
                        if ($table == $model) {
                            $random[$field] = $val;
                        } else {
                            $random[$table][$field] = $val;
                        }
                        break;
                    case 'int':
                    case 'tinyint':
                        $val = 1;
                        if ($table == $model) {
                            $random[$field] = $val;
                        } else {
                            $random[$table][$field] = $val;
                        }
                        break;
                    default:
                        $val = 'some';
                        if ($table == $model) {
                            $random[$field] = $val;
                        } else {
                            $random[$table][$field] = $val;
                        }
                }
            }
        }
        return $random;
    }
}