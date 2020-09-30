<?php


namespace Neoan3\Provider\MySql;


interface Database
{
    function connect($arguments = []);
    function easy($selectString, $conditions = [], $callFunctions = []);
    function smart($tableOrString, $conditions = null, $callFunctions = null);
}