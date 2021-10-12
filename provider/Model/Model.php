<?php


namespace Neoan3\Provider\Model;


/**
 * Interface MySqlModel
 * @package Neoan3\Provider\MySql
 * @method static get(string $id)
 * @method static create(array $modelArray)
 * @method static update(array $modelArray)
 * @method static find(array $conditionArray, array $callFunctions = [])
 * @method static delete(string $id, bool $hard = false)
 */
interface Model
{
    public static function init(array $providers);
}