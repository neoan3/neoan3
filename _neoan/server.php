<?php
ini_set('display_errors', true);
ini_set('error_reporting', E_ALL);

class ServerRoute
{
    private $appLevel;
    private $queryString;
    private $pureUri;
    private $request;

    function __construct()
    {
        $this->appLevel = dirname(__DIR__);
        $this->queryString = $_SERVER["REQUEST_URI"];
        $this->pureUri = strtok(str_replace('index.php/', '', $this->queryString), '?');
        $this->request = preg_replace('/^\//', '', $this->pureUri);
        return $this;
    }

    private function writeAction($subtract = '')
    {
        $_GET['action'] = substr($this->request, strlen($subtract));
    }

    function route()
    {
        if (file_exists($this->appLevel . $this->pureUri) && !is_dir($this->appLevel . $this->pureUri)) {
            echo file_get_contents($this->appLevel . $this->pureUri);
        } elseif (preg_match('/api.v1\/(.*)$/', $this->queryString)) {
            require_once $this->appLevel . '/_neoan/base/Api.php';
        } elseif (preg_match('/serve.file\/(.*)$/', $this->queryString)) {
            $this->writeAction('/serve.file/');
            require_once $this->appLevel . '/_neoan/base/FileServe.php';
        } elseif (preg_match('/^node_modules\/(.*)$/', $this->queryString)) {
            include $this->appLevel . DIRECTORY_SEPARATOR . '_neoan/base/Node.php';
        } else {
            $this->writeAction();
            include $this->appLevel . DIRECTORY_SEPARATOR . 'index.php';
        }
    }
}

$serve = new ServerRoute();
$serve->route();