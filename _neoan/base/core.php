<?php

namespace Neoan3;

// catch all errors?
use ErrorException;
use Neoan3\Core\ReflectionWrapper;

function exception_error_handler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errfile)) {
        return;
    }
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);

}

//set_error_handler("exception_error_handler");


require_once(dirname(__FILE__) . '/_includes.php');
$route = new Core\Route();

####################################################
#
# RUN
#
$namespace = $route->call;
$consumer = __NAMESPACE__ . "\\Component\\$namespace\\${namespace}Controller";
Core\Event::dispatch('Core::beforeInit', ['component' => $route->call]);
$r = new ReflectionWrapper($consumer, 'init');
$r->dispatchAttributes(__NAMESPACE__);
$run = new $consumer;
$run->init();

