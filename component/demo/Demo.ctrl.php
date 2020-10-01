<?php

namespace Neoan3\Components;

use Neoan3\Provider\MySql\Database;
use Neoan3\Core\Unicore;

/**
 * Class Demo
 * @package Neoan3\Components
 */
class Demo extends Unicore
{
    /**
     * @var Database|null
     */
    private ?DataBase $db;

    /**
     * Demo constructor.
     * This constructor is only necessary if providers a decoupled
     * @param Database|null $db
     */
    public function __construct(DataBase $db = null)
    {
        $this->db = $db;
    }

    /**
     * Route call (Singleton style)
     */
    function init()
    {
        $info = json_decode(file_get_contents(path . '/composer.json'), true);
        $info['installation'] = path;
        $this
            ->registerProvider($this->db)
            ->uni('demo')
            ->addHead('title', 'neoan3 default')
            ->hook('main', 'demo', $info)
            ->output();
    }
}
