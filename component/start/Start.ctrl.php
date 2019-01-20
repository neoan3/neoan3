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

class Start extends Unicore {
    function init(){
        $this->uni('kit')
            ->hook('main','start')
            ->output();
    }
}