<?php


namespace Neoan3\Provider\MySql;



use Neoan3\Apps\DbOOP;

class DatabaseWrapper extends DbOOP implements Database
{
    function connect($arguments = [])
    {
        $this->setEnvironment($arguments);
    }
    function pure($sql, $conditions=null, $extra=null)
    {
        return $this->smart('>' . $sql, $conditions, $extra);
    }
    function getNextId()
    {
        return $this->smart('>SELECT UPPER(REPLACE(UUID(),"-","")) as id')[0]['id'];
    }
}