<?php
/**
 * Created by PhpStorm.
 * User: sroehrl
 * Date: 2/4/2019
 * Time: 1:36 PM
 */

namespace Neoan3\Frame;

use Neoan3\Core\Serve;
use Neoan3\Provider\MySql\Database;

/**
 * Class Demo
 * @package Neoan3\Frame
 */
class Demo extends Serve
{
    /**
     * @var Database
     */
    protected Database $db;

    /**
     * Demo constructor.
     * @param Database|null $db
     */
    public function __construct(Database $db = null)
    {
        parent::__construct();
    }

    /**
     * @return array
     */
    function constants()
    {
        return [
            'base' => [base],
            'link' => [
                [
                    'sizes' => '32x32',
                    'type' => 'image/png',
                    'rel' => 'icon',
                    'href' => 'asset/neoan-favicon.png'
                ]
            ],
            'stylesheet' => [
                '' . base . 'frame/demo/demo.css',
                'https://cdn.jsdelivr.net/npm/gaudiamus-css@1.1.0/css/gaudiamus.min.css',
            ]
        ];
    }
}
