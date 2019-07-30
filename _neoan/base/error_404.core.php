<?php

namespace Neoan3\Components;

use Neoan3\Core\Unicore;

/**
 * Class error_404
 * @package Neoan3\Components
 */
class error_404 extends Unicore
{
    /**
     * Hooking into the default router logic
     */
    function init()
    {
        error_reporting(E_ALL ^ E_NOTICE);
        ini_set('display_errors', 1);
        header("HTTP/1.0 404 Not Found");
        if (defined('default_404')) {
            $class = __NAMESPACE__ . '\\' . ucfirst(default_404);
            $run = new $class();
            $run->init();
            exit();
        }
        $this->uni()->addHead('title', 'Not found')
             ->callback($this, 'action')->output();
    }

    /**
     * @param $uni
     * @param array $args
     */
    function action($uni, $args = [])
    {
        $uni->main = '
			<h3>404 - Nothing can be found here</h3>
			<h5>Possible reasons:</h5>
			<p>Your mistake: wrong link, typo in your browser-bar etc.</p>
			<p>My mistake: not built yet, wrong link provided etc.</p>
			<p>ANYWAY: ' . a(base, 'TAKE ME HOME') . '</p>';
    }
}