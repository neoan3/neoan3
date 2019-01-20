<?php
/**
 * Created by PhpStorm.
 * User: Stefan
 * Date: 1/20/2019
 * Time: 3:52 PM
 * @property layout uni
 */

namespace Neoan3\Components;

use Neoan3\Core\Unicore;

class NotFound extends Unicore {
    function init(){
        $this->uni('kit')
            ->hook('main','notFound',['sub'=>sub(0)])
            ->output();
    }
}