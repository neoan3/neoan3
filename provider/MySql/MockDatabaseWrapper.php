<?php


namespace Neoan3\Provider\MySql;


class MockDatabaseWrapper extends DatabaseWrapper
{
    private array $results = [];
    private int $nextStep = 0;
    private array $modelStructure;

    public function __construct($environmentVariables = [], $modelStructure = [])
    {
        parent::__construct($environmentVariables);
        $this->modelStructure = $modelStructure;
    }

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
    function smart($selectString, $conditions = [], $callFunctions = [])
    {
        $result = $this->results[$this->nextStep];
        $this->nextStep++;
        return $result;
    }
    function mockModel($model)
    {
        $transform = new Transform($model, $this, $this->modelStructure);
        $random = [];
        foreach ($transform->modelStructure as $table => $fields) {
            foreach ($transform->modelStructure[$table] as $field => $specs) {
                switch (preg_replace('/[^a-z]/i', '', $specs['type'])) {
                    case 'binary':
                        $val = '123456789';
                        if ($table == $model) {
                            $random[$field] = $val;
                        } else {
                            $random[$table][0][$field] = $val;
                        }
                        break;
                    case 'timestamp':
                    case 'datetime':
                        $now = time();
                        $val = date('Y-m-d H:i:s', $now);
                        if ($table == $model) {
                            $random[$field] = $val;
                        } else {
                            $random[$table][0][$field] = $val;
                        }
                        break;
                    case 'int':
                    case 'tinyint':
                        $val = 1;
                        if ($table == $model) {
                            $random[$field] = $val;
                        } else {
                            $random[$table][0][$field] = $val;
                        }
                        break;
                    default:
                        $val = 'some';
                        if ($table == $model) {
                            $random[$field] = $val;
                        } else {
                            $random[$table][0][$field] = $val;
                        }
                }
            }
        }
        return $random;
    }

    /**
     * @param $modelName
     * @param null $entity
     * @return array|mixed
     */
    function mockGet($modelName, $entity=null)
    {
        $model = $this->mockModel($modelName);
        if($entity){
            $this->registerResult([$entity]);
        } else {
            $this->registerResult([$model]);
        }
        foreach ($model as $key => $value){
            if(is_array($value)){
                if($entity){
                    $this->registerResult($entity[$key]);
                } else {
                    $this->registerResult($model[$key]);
                }

            }
        }
        return $entity ? $entity : $model;
    }

    /**
     * @param $modelName
     * @param $entity
     * @return array|mixed
     */
    function mockUpdate($modelName, $entity)
    {
        foreach ($entity as $potential => $values){
            if(is_array($values)){
                for($i = 0; $i < count($values); $i++){
                    $this->registerResult('update');
                }
            }
        }
        $this->registerResult('update main');

        return $this->mockGet($modelName, $entity);
    }
}