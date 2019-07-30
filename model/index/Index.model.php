<?php

namespace Neoan3\Model;

class IndexModel
{

    static function first($ask)
    {
        if (!empty($ask)) {
            return $ask[0];
        } else {
            return [];
        }
    }
}
