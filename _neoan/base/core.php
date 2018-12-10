<?php

// catch all errors?
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    if (!(error_reporting() & $errfile)) {
        return;
    }
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);

}
//set_error_handler("exception_error_handler");

require_once(dirname(__FILE__) . '/Unicore.php');
include_once(dirname(__FILE__).'/Route.php');
$route = new Route();
include_once(path . '/vendor/autoload.php');
include_once(neoan_path . '/base/functions.php');
include_once(neoan_path . '/layout/serve.output.php');
include_once(neoan_path . '/base/loader.php');

####################################################
#
# RUN
#

$consumer = __NAMESPACE__ . '\\Neoan3\\Components\\' . $route->call;
$run = new $consumer;
$run->init();

