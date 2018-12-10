<?php
//session_save_path(?);
// debugging
ini_set('error_reporting', E_ALL ^E_NOTICE);
ini_set('display_errors', true);
ini_set('display_startup_errors',true);
error_reporting(E_ALL);

/********************COPYRIGHT**NOTICE***********************
 
 This script was created by NEOAN under CCA 3.0 in 2013
 Visit neoan.us
 
 ***********************************************************************/
$start = microtime(true);
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header("X-Frame-Options: {$_SERVER['HTTP_ORIGIN']}");
} else {
    header("X-Frame-Options: GOFORIT");
}

require_once(dirname(__FILE__).'/_neoan/base/core.php');
$done = microtime(true)- $start;
echo '<!-- delivered in ' . number_format($done,2) . ' seconds -->';
exit();