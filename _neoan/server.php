<?php
$queryString = $_SERVER["REQUEST_URI"];
$_GET['action'] = preg_replace('/^\//','',$queryString);
$appLevel = dirname(__DIR__);

if (file_exists($appLevel.$queryString)) {
    return false;
} elseif(preg_match('/api.v1\/(.*)$/', $queryString)) {
    require_once $appLevel . '/_neoan/base/Api.php';
}elseif(preg_match('/file.serve\/(.*)$/', $queryString)) {
    require_once $appLevel . '/_neoan/base/FileServe.php';
} elseif (preg_match('/^node_modules\/(.*)$/', $queryString)){
    include $appLevel . DIRECTORY_SEPARATOR . '_neoan/base/Node.php';
} else {
    include $appLevel . DIRECTORY_SEPARATOR . 'index.php';
}