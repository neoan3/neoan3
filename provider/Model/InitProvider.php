<?php
namespace Neoan3\Provider\Model;

use Neoan3\Provider\MySql\DatabaseWrapper;

trait InitProvider {
    /**
     * @param array $providers
     */
    public static function init(array $providers)
    {
        foreach ($providers as $provider){
            if($provider instanceof DatabaseWrapper){
                self::$db = $provider;
            }
        }
    }
}