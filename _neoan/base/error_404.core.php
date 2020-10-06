<?php

namespace Neoan3\Component\Error_404;

use Neoan3\Core\Unicore;

/**
 * Class error_404
 * @package Neoan3\Components
 */
class Error_404Controller extends Unicore
{
    /**
     * Hooking into the default router logic
     */
    function init()
    {
        header("HTTP/1.0 404 Not Found");
        if (defined('default_404')) {
            $class = 'Neoan3\\Component\\' . ucfirst(default_404) . '\\' . ucfirst(default_404) . 'Controller';
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
        $origin = $_SERVER['HTTP_REFERER'] ?? '';
        $requested = $_SERVER['REQUEST_URI'] ?? '';
        $uni->main = '
			<h3>404 - Nothing can be found here</h3>
			<p>Requested: ' . $requested . '</p>
			<p>Traceback: ' . $origin . '</p>
			<p>Additionally, Neoan3 was not able to locate a custom 404</p>';
    }
}