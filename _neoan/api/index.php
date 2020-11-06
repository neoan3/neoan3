<?php
$apiLoader = $_SERVER['REQUEST_URI'];
preg_match('/api\.([a-z0-9]+)\//',$apiLoader,$version);
if(isset($version[1])){
    require_once (dirname(dirname(__DIR__)) . '/vendor/autoload.php');
    require_once(dirname(dirname(__FILE__)) . '/base/_includes.php');

    $apiClass = '\\Neoan3\\Api\\' . ucfirst($version[1]);
    if(!class_exists($apiClass)){
        echo "API version $version[1] not installed";
        die();
    }

    new \Neoan3\Core\Route();
    $api = new $apiClass();
    $api->apiRoute();

    exit();
} else {
    http_response_code(400);
    echo "Malformed request: missing endpoint or wrong API-version";
    die();
}
