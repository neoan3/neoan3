<?php
namespace Neoan3\Model;

trait Init {
    /**
     * @param array $providers
     */
    public static function init(array $providers)
    {
        foreach ($providers as $key => $provider){
            if($key === 'db'){
                self::$db = $provider;
            }
        }
    }
}