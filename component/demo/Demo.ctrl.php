<?php

namespace Neoan3\Components;

use Neoan3\Core\Unicore;

class Demo extends Unicore
{
    function init()
    {
        $info = json_decode(file_get_contents(path.'/composer.json'),true);
        $this->uni('demo')
             ->hook('main', 'demo', $info)
             ->output();
    }
}
