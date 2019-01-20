<?php
/**
 * Created by PhpStorm.
 * User: Stefan
 * Date: 1/20/2019
 * Time: 5:53 PM
 * @property layout uni
 */

namespace Neoan3\Components;

use Neoan3\Core\Unicore;

/**
 * Class Start
 * @package Neoan3\Components
 */
class Start extends Unicore {
    /**
     * Route constructor
     */
    function init(){
        $this->uni('kit')
            ->hook('main','start')
            ->output();
    }
}