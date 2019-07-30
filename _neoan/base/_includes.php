<?php
/**
 * User: Stefan
 * Date: 12/15/2018
 * Time: 9:41 PM
 */
define('path', dirname(dirname(dirname(__FILE__))));
define('neoan_path', dirname(dirname(__FILE__)));
define('asset_path', dirname(dirname(dirname(__FILE__))) . '/asset');

require_once(dirname(__FILE__) . '/Unicore.php');
include_once(dirname(__FILE__) . '/Route.php');

include_once(path . '/vendor/autoload.php');
include_once(neoan_path . '/base/functions.php');
include_once(neoan_path . '/layout/Serve.output.php');
include_once(neoan_path . '/layout/RouteException.php');
include_once(neoan_path . '/base/loader.php');
