<?php


namespace Neoan3\Provider\MySql;



use Exception;
use Neoan3\Apps\DbException;
use Neoan3\Apps\DbOOP;

class DatabaseWrapper extends DbOOP implements Database
{
    function connect($arguments = [])
    {
        $this->setEnvironment($arguments);
    }
    function pure($sql, $conditions=null, $extra=null)
    {
        try{
            return $this->smart('>' . $sql, $conditions, $extra);
        } catch (DbException $e){
            throw new Exception($e->getMessage());
        }

    }
    function getNextId()
    {
        try{
            return $this->smart('>SELECT UPPER(REPLACE(UUID(),"-","")) as id')[0]['id'];
        } catch (DbException $e){
            throw new Exception($e->getMessage());
        }
    }
    function easy($selectString, $conditions = [], $callFunctions = [])
    {
        try{

            return parent::easy($selectString, $conditions, $callFunctions);
        } catch (DbException $e){
            throw new Exception($e->getMessage());
        }
    }
}