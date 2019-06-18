<?php
$queryString = $_SERVER["REQUEST_URI"];

$appLevel = dirname(__DIR__);

function writeAction($subtract = '') {
    $request = preg_replace('/^\//', '', strtok($_SERVER["REQUEST_URI"], '?'));
    $_GET['action'] = substr($request, strlen($subtract));
}

if(file_exists($appLevel . $queryString) && !is_dir($appLevel . $queryString)) {
    echo file_get_contents($appLevel . $queryString);
} elseif(preg_match('/api.v1\/(.*)$/', $queryString)) {
    require_once $appLevel . '/_neoan/base/Api.php';
} elseif(preg_match('/serve.file\/(.*)$/', $queryString)) {
    writeAction('/serve.file/');
    require_once $appLevel . '/_neoan/base/FileServe.php';
} elseif(preg_match('/^node_modules\/(.*)$/', $queryString)) {
    include $appLevel . DIRECTORY_SEPARATOR . '_neoan/base/Node.php';
} else {
    writeAction();
    include $appLevel . DIRECTORY_SEPARATOR . 'index.php';
}
