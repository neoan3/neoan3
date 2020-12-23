<?php
ini_set('display_errors', true);
ini_set('error_reporting', E_ALL);

class ServerRoute
{
    private string $appLevel;
    private $queryString;
    private string $pureUri;
    public $request;

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
    private function writeHeader($fileName){
        $parts = explode('.', $fileName);
        $ext = end($parts);
        switch ($ext) {
            case 'js':
                header('Content-Type: text/javascript');
                break;
            case 'css':
                header('Content-Type: text/css');
                break;
            case 'svg':
                header('Content-Type: image/svg+xml');
                break;
        }
    }

    function route()
    {
        if (preg_match('/node_modules\/(.*)$/', $this->queryString)) {
            $this->writeAction('/node_modules');
            include $this->appLevel . DIRECTORY_SEPARATOR . '_neoan/base/Node.php';
        } elseif (file_exists($this->appLevel . $this->pureUri) && !is_dir($this->appLevel . $this->pureUri)) {
            $this->writeHeader($this->pureUri);
            echo file_get_contents($this->appLevel . $this->pureUri);
        } elseif (preg_match('/api.(.*)$/', $this->queryString)) {
            require_once $this->appLevel . '/_neoan/api/index.php';
        } elseif (preg_match('/serve.file\/(.*)$/', $this->queryString)) {
            $this->writeAction('/serve.file/');
            require_once $this->appLevel . '/_neoan/base/FileServe.php';
        } else {
            $this->writeAction();
            include $this->appLevel . DIRECTORY_SEPARATOR . 'index.php';
        }
    }
}

$serve = new ServerRoute();
$serve->route();