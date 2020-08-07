<?php
/**
 * Created by PhpStorm.
 * User: sroehrl
 * Date: 2/4/2019
 * Time: 1:36 PM
 */

namespace Neoan3\Frame;

use Neoan3\Core\Serve;

class Demo extends Serve
{
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
