<?php


namespace Neoan3\Provider\MySql;


/**
 * Class Transform
 * @package Neoan3\Provider\MySql
 */
class Transform
{
    /**
     * @var string
     */
    private string $modelName;
    /**
     * @var array|mixed
     */
    public array $modelStructure;
    /**
     * @var Database
     */
    private Database $db;

    /**
     * @var array|null
     */
    private ?array $getReader = null;

    /**
     * Transform constructor.
     * @param $model
     * @param Database $db
     * @param null $modelStructure
     */
    function __construct($model, Database $db, $modelStructure = null)
    {
        $this->modelName = $model;
        $this->modelStructure = $modelStructure ?? $this->readMigrate();
        $this->db = $db;
    }

    /**
     * @param $result
     * @param $runner
     * @param $row
     */
    private function formatResult(&$result, $runner, $row)
    {
        foreach ($this->modelStructure as $table => $fields){
            // account for empty sub model
            if(empty($row[$table.'_id'])){
                if(!isset($result[$table])){
                    $result[$table] = [];
                }
                continue;
            }
            // avoid duplication in complex models
            if($table !== $this->modelName && isset($result[$table]) && $this->duplicationCheck($result[$table], $row[$table . '_id'])){
                continue;
            }
            $this->assignResult($result, $table, $fields, $row, $runner);

        }
    }

    /**
     * @param $ids
     * @return \Generator
     */
    function getGenerator($ids): \Generator
    {
        foreach ($ids as $id){
            yield $this->get($id['id']);
        }
    }

    /**
     * @param $id
     * @return array
     */
    function get($id)
    {
        if(!$this->getReader){
            $this->getReader = $this->readSql();
        }
        $result = [];
        $sql = $this->getReader['query'] . $this->getReader['joins'] . ' WHERE `'. $this->modelName .'`.`id` = UNHEX({{id}})' . $this->getReader['condition'];
        $pureResult = $this->db->smart('>'.$sql, ['id' => $id]);

        foreach ($pureResult as $i => $row){
            $this->formatResult($result, $i, $row);
        }
        return $result;
    }

    /**
     * @param $inserts
     * @return array
     * @throws \Exception
     */
    function create($inserts): array
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

    /**
     * @param $entity
     * @return array
     */
    function update($entity): array
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

    /**
     * @param string $id
     * @param bool $hard
     * @return bool
     */
    function delete(string $id, bool $hard = false): bool
    {
        $entity = $this->get($id);
        // main
        $this->deleteRow($this->modelName, $entity, $hard);
        // subs
        foreach ($entity as $tableOrField => $fieldOrFields){
            if(is_array($fieldOrFields)){
                foreach ($fieldOrFields as $row){
                    $this->deleteRow($tableOrField, $row, $hard);
                }
            }
        }
        return !empty($entity);
    }

    /**
     * @param $condition
     * @param array $callFunctions
     * @return array
     */
    function find($condition, $callFunctions = []): array
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
        $callFunctions = array_merge([
            'orderBy'=>[$this->modelName . '.id', 'DESC']],
            $callFunctions
        );
        $hits = $this->db->easy($join, $condition, $callFunctions);

        $return = [];
        foreach ($this->getGenerator($hits) as $hit){
            $return[] = $hit;
        }

        return $return;
    }

    /**
     * @param string $table
     * @param array $row
     * @param bool $hard
     */
    private function deleteRow(string $table, array $row, bool $hard)
    {
        if(!array_key_exists('delete_date', $row)  || $hard){
            $this->db->smart('>DELETE FROM `' . $table . '` WHERE id = UNHEX({{id}})',['id'=>$row['id']]);
        } else {
            $this->db->smart($table, ['delete_date'=>'.'], ['id'=> '$' . $row['id']]);
        }
    }

    /**
     * @param $table
     * @param $fieldValueArray
     * @return array
     */
    private function validate($table, $fieldValueArray): array
    {
        $returnArray = [];
        foreach ($fieldValueArray as $field => $value){
            if(isset($this->modelStructure[$table][$field])){
                switch ($this->cleanType($this->modelStructure[$table][$field]['type'])){
                    case 'binary':
                        $returnArray[$field] = '$' . $value;
                        break;
                    case 'datetime':
                        if (is_numeric($value)) {
                            $value = date('Y-m-d H:i:s', round($value / 1000));
                        } elseif($value === '.') {
                            $returnArray[$field] = $value;
                            break;
                        } else {
                            $value = preg_replace("/\s[A-Z]{3}\s[0-9]{4}\s\([^)]+\)/", '', $value);
                        }
                        $date_array = date_parse($value);
                        $returnArray[$field] = !empty($date_array['errors']) ? null : date('Y-m-d H:i:s', mktime($date_array['hour'], $date_array['minute'], $date_array['second'], $date_array['month'], $date_array['day'], $date_array['year']));
                        break;
                    case 'decimal':
                        $returnArray[$field] = (is_int($value) ? '=' : '') . $value;
                        break;
                    default:
                        $returnArray[$field] = $value;
                }
            }
        }
        return $returnArray;
    }

    /**
     * @return mixed
     */
    private function readMigrate(): mixed
    {
        return json_decode(file_get_contents(path . "/model/" . ucfirst($this->modelName) . "/migrate.json"), true);
    }

    /**
     * @return array
     */
    private function readSql(): array
    {
        $pureQueryString = 'SELECT ';
        $joins = ' FROM `' . $this->modelName .'`';
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
                            $joins .= " AND `$table`.`$field` IS NULL ";
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

    /**
     * @param $type
     * @return string|string[]|null
     */
    private function cleanType($type)
    {
        return preg_replace('/[^a-z]/','', $type);
    }

    /**
     * @param $subModelResults
     * @param $id
     * @return bool
     */
    private function duplicationCheck($subModelResults, $id): bool
    {
        foreach ($subModelResults as $existing){
            if($existing['id'] === $id){
                return true;
            }
        }
        return false;
    }

    /**
     * @param $result
     * @param string $table
     * @param array $fields
     * @param array $row
     * @param int $runner
     */
    private function assignResult(&$result, string $table, array $fields, array $row, int $runner)
    {
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