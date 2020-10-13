<?php


namespace Neoan3\Provider\MySql;


class MockDatabaseWrapper extends DatabaseWrapper
{
    private array $results = [];
    private int $nextStep = 0;
    private ?array $mockModelStructure;

    public function __construct($environmentVariables = [], $mockModelStructure = null)
    {
        parent::__construct($environmentVariables);
        $this->mockModelStructure = $mockModelStructure;
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
        $transform = new Transform($model, $this, $this->mockModelStructure);
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
                    case 'date':
                    case 'datetime':
                        $now = time();
                        $val = date('Y-m-d H:i:s', $now);
                        if ($table == $model) {
                            $random[$field] = $val;
                            $random[$field."_st"] = $now;

                        } else {
                            $random[$table][0][$field] = $val;
                            $random[$table][0][$field.'_st'] = $now;
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
        $iterator = $entity ?? $model;
        $sqlResult = ['id' => $iterator['id']];

        foreach ($iterator as $key => $value){
            if(is_array($value)){
                foreach ($value[0] as $subKey => $subValue){
                    $sqlResult[$key.'_'.$subKey] = $subValue;
                }
            } else {
                $sqlResult[$modelName.'_'.$key] = $value;
            }
        }
        $this->registerResult([$sqlResult]);

        return $entity ? $entity : $model;
    }

    /**
     * @param $modelName
     * @param $entity
     * @return array|mixed
     */
    function mockUpdate($modelName,$entity)
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

    function mockFind($modelName, $entity=null)
    {
        $this->registerResult([['id' => $entity ? $entity['id'] : 'someUUId']]);
        return $this->mockGet($modelName, $entity);
    }
    function mockDelete($modelName, $entity=null)
    {
        $old = $this->mockGet($modelName, $entity);
        $this->registerResult('delete main');
        foreach ($old as $potential => $values){
            if(is_array($values)){
                $this->registerResult('delete sub');
            }
        }

    }
}