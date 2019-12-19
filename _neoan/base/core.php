<?php

namespace Neoan3;

// catch all errors?
function exception_error_handler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errfile)) {
        return;
    }
    throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);

}

//set_error_handler("exception_error_handler");


require_once(dirname(__FILE__) . '/_includes.php');
$route = new Core\Route();

####################################################
#
# RUN
#

$consumer = __NAMESPACE__ . '\\Components\\' . $route->call;
Core\Event::dispatch('Core::beforeInit', ['component' => $route->call]);
$run = new $consumer;
$run->init();

