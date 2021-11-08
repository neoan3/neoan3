<?php

namespace Neoan3;


use ErrorException;
use Neoan3\Core\ReflectionWrapper;

/**
 * @throws ErrorException
 */
function exception_error_handler($errno, $errorStr, $errorFile, $errorLine)
{
    if (!(error_reporting() & $errorFile)) {
        return;
    }
    throw new ErrorException($errorStr, $errno, 0, $errorFile, $errorLine);

}
// elevate all issues to errors?
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
$returns = $run->init();
if($returns){
    echo "<pre>";
    print_r($returns);
}

