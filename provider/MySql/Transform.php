<?php


namespace Neoan3\Provider\MySql;



use Neoan3\Apps\Db;

class Transform
{
    private string $modelName;
    public array $modelStructure;
    /**
     * @var Database
     */
    private Database $db;

    private ?array $getReader = null;

    function __construct($model, Database $db, $modelStructure = null)
    {
        $this->modelName = $model;
        $this->modelStructure = $modelStructure ?? $this->readMigrate();
        $this->db = $db;
    }
    private function formatResult(&$result, $runner, $row)
    {
        foreach ($this->modelStructure as $table => $fields){
            foreach ($fields as $fieldName => $specs){

                if($table == $this->modelName){
                    $result[$fieldName] = $row[$table .'_' . $fieldName];
                    if(in_array($this->cleanType($specs['type']),['timestamp','date','datetime'])){
                        $result[$fieldName . '_st'] = $row[$table .'_' . $fieldName . '_st'];
                    }
                } else {
                    $result[$table][$runner][$fieldName] = $row[$table .'_' . $fieldName];
                    if(in_array($this->cleanType($specs['type']),['timestamp','date','datetime'])){
                        $result[$table][$runner][$fieldName . '_st'] = $row[$table .'_' . $fieldName . '_st'];
                    }
                }
            }

        }
    }
    function getGenerator($ids){
        foreach ($ids as $id){
            yield $this->get($id['id']);
        }
    }

    function get($id)
    {
        if(!$this->getReader){
            $this->getReader = $this->readSql();
        }
        $result = [];
        $sql = $this->getReader['query'] . $this->getReader['joins'] . ' WHERE post.id = UNHEX({{id}})' . $this->getReader['condition'];
        $pureResult = $this->db->smart('>'.$sql, ['id' => $id]);

        foreach ($pureResult as $i => $row){
            $this->formatResult($result, $i, $row);
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
                    $extra = null;
                    if(isset($value['id'])){
                        $extra = ['id' => '$' . $value['id']];
                    } else {
                        $value[$this->modelName . '_id'] = $entity['id'];
                    }
                    $this->db->smart($potential, $this->validate($potential, $value), $extra);
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
        $joinTables = [];
        foreach ($condition as $tableField => $value){
            if( preg_match('/[a-z_]+/', $tableField, $matches) === 1){
                $joinTables[] = $matches[0];
            }
        }

        $join = $this->modelName . '.id';
        foreach ($this->modelStructure as $table => $fields){
            if($table !== $this->modelName && in_array($table, $joinTables)){
                $join .= " $table.id:${table}_id";
            }
        }
        $hits = $this->db->easy($join, $condition, ['groupBy' => [$this->modelName . '.id',$this->modelName . '.insert_date']]);
        $return = [];
        foreach ($this->getGenerator($hits) as $hit){
            $return[] = $hit;
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
    private function readMigrate()
    {
        return json_decode(file_get_contents(path . "/model/$this->modelName/migrate.json"), true);
    }
    private function readSql()
    {
        $pureQueryString = 'SELECT ';
        $joins = ' FROM ' . $this->modelName;
        $condition = '';
        $modelName = $this->modelName;
        foreach ($this->modelStructure as $table => $any){
            if($table !== $modelName){
                $joins .= " LEFT JOIN `$table` ON `$table`.`${modelName}_id` = `$modelName`.`id` ";
            }
            foreach ($this->modelStructure[$table] as $field => $specs){
                switch ($this->cleanType($specs['type'])){
                    case 'binary':
                        $pureQueryString .= "HEX(`${table}`.`${field}`) as ${table}_$field, ";
                        break;
                    case 'timestamp':
                    case 'date':
                    case 'datetime':
                        if(($field == 'delete_date' || $field == 'deleteDate') && $table !== $modelName){
                            $condition .= " AND `$table`.`$field` IS NULL ";
                        }
                        $pureQueryString .= "UNIX_TIMESTAMP(`${table}`.`${field}`)*1000 as ${table}_${field}_st, ";
                    default:
                        $pureQueryString .= "`${table}`.`${field}` as ${table}_${field}, ";
                        break;
                }

            }
        }
        return ['query' => substr($pureQueryString, 0 , -2), 'joins' => $joins, 'condition' => $condition];
    }
    private function cleanType($type)
    {
        return preg_replace('/[^a-z]/','', $type);
    }
}