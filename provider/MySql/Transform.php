<?php


namespace Neoan3\Provider\MySql;


use Neoan3\Model\IndexModel;

class Transform
{
    private string $modelName;
    public array $modelStructure;
    /**
     * @var Database
     */
    private Database $db;

    /**
     * Transform constructor.
     * @param $model
     * @param Database $db
     * @param array $modelStructure
     */
    function __construct($model, Database $db, array $modelStructure = [])
    {
        $this->modelName = $model;
        if(empty($modelStructure)){
            $this->readMigrate();
        } else {
            $this->modelStructure = $modelStructure;
        }
        $this->db = $db;
    }
    function get($id)
    {
        $reader = $this->readSql();
        $result = IndexModel::first($this->db->easy($reader[$this->modelName], ['id' => '$' . $id]));
        foreach ($reader as $table => $sql){
            if($table !== $this->modelName){
                $result[$table] = $this->db->easy($sql, [$this->modelName . '_id' => '$' . $id]);
            }
        }
        return $result;
    }
    function create($inserts)
    {
        $id = $this->db->getNextId();
        $main = [];
        foreach ($inserts as $potential => $value){
            if(is_array($value)){
                foreach ($value as $subModel){
                    $subModel[$this->modelName . '_id'] = $id;
                    $this->db->smart($potential, $this->validate($potential, $subModel));
                }
            } else {
                $main[$potential] = $value;
            }
        }
        $main['id'] = $id;
        $this->db->smart($this->modelName, $this->validate($this->modelName, $main));
        return $this->get($id);
    }
    function update($entity)
    {
        $main = [];
        foreach ($entity as $potential => $values){
            if(is_array($values)){
                foreach($values as $value){
                    $this->db->smart($potential, $this->validate($potential, $value), ['id' => '$' . $value['id']]);
                }
            } else {
                $main[$potential] = $values;
            }
        }
        $this->db->smart($this->modelName, $this->validate($this->modelName, $main), ['id' => '$' . $entity['id']]);
        return $this->get($entity['id']);
    }
    function find($condition)
    {
        $join = $this->modelName . '.id';
        foreach ($this->modelStructure as $table => $fields){
            if($table !== $this->modelName){
                $join .= " $table.id:${table}_id";
            }
        }
        $hits = $this->db->easy($join, $condition);
        $return = [];
        foreach ($hits as $hit){
            $return[] = $this->get($hit['id']);
        }
        return $return;
    }

    private function validate($table, $fieldValueArray)
    {
        $returnArray = [];
        foreach ($fieldValueArray as $field => $value){
            if(isset($this->modelStructure[$table][$field])){
                switch ($this->cleanType($this->modelStructure[$table][$field]['type'])){
                    case 'binary':
                        $returnArray[$field] = '$' . $value;
                        break;
                    default:
                        $returnArray[$field] = $value;
                }
            }
        }
        return $returnArray;
    }
    public function readMigrate()
    {
         $this->modelStructure = json_decode(file_get_contents(path . "/model/$this->modelName/migrate.json"), true);
    }
    private function readSql()
    {
        $res = [];

        foreach ($this->modelStructure as $table => $any){
            $mainStr = '';
            foreach ($this->modelStructure[$table] as $field => $specs){
                switch ($this->cleanType($specs['type'])){
                    case 'binary':
                        $mainStr .= '$' . "${table}.${field}:$field ";
                        break;
                    case 'timestamp':
                    case 'datetime':
                        $mainStr .= "${table}.$field ";
                        $mainStr .= "#${table}.${field}:${field}_st ";
                        break;
                    default:
                        $mainStr .= "${table}.$field ";
                        break;
                }

            }
            $res[$table] = substr($mainStr, 0 , -1);
        }
        return $res;
    }
    private function cleanType($type)
    {
        return preg_replace('/[^a-z]/','', $type);
    }
}