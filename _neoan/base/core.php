<?php

// catch all errors?
function exception_error_handler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errfile)) {
        return;
    }
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);

}

//set_error_handler("exception_error_handler");


require_once(dirname(__FILE__) . '/_includes.php');
$route = new Route();

####################################################
#
# RUN
#

$consumer = __NAMESPACE__ . '\\Neoan3\\Components\\' . $route->call;
$run = new $consumer;
$run->init();

