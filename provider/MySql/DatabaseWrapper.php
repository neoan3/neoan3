<?php


namespace Neoan3\Provider\MySql;



use Exception;
use Neoan3\Apps\DbException;
use Neoan3\Apps\DbOOP;

/**
 * Class DatabaseWrapper
 * @package Neoan3\Provider\MySql
 */
class DatabaseWrapper extends DbOOP implements Database
{
    /**
     * @param array $arguments
     * @throws DbException
     */
    function connect($arguments = [])
    {
        $this->setEnvironment($arguments);
    }

    /**
     * @param $sql
     * @param null $conditions
     * @param null $extra
     * @return array|int|mixed
     * @throws Exception
     */
    function pure($sql, $conditions=null, $extra=null)
    {
        try{
            return $this->smart('>' . $sql, $conditions, $extra);
        } catch (DbException $e){
            throw new Exception($e->getMessage());
        }

    }

    /**
     * @return mixed
     * @throws Exception
     */
    function getNextId()
    {
        try{
            return $this->smart('>SELECT UPPER(REPLACE(UUID(),"-","")) as id')[0]['id'];
        } catch (DbException $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $selectString
     * @param array $conditions
     * @param array $callFunctions
     * @return array|int|mixed|string
     * @throws Exception
     */
    function easy($selectString, $conditions = [], $callFunctions = [])
    {
        try{

            return parent::easy($selectString, $conditions, $callFunctions);
        } catch (DbException $e){
            throw new Exception($e->getMessage());
        }
    }
}