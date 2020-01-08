<?php

namespace Neoan3\Core;

require_once(dirname(__FILE__) . '/_includes.php');


new Route();
$api = new Api();
$api->apiRoute();

exit();

/**
 * Class Api
 */
class Api
{
    /**
     * @var
     */
    public $stream;
    /**
     * @var array
     */
    public $header = [];
    /**
     * @var
     */
    private $responseCode;

    /**
     * Api constructor.
     */
    function __construct()
    {
        $data = file_get_contents('php://input');
        if (!empty($data)) {
            $divide = json_decode($data, true);
            // secure here!!
            $this->stream = $divide;
        } elseif (!empty($_FILES)) {
            $this->stream = $_POST;
        } else {
            $this->stream = $_REQUEST;
        }
        $this->requestMethod();
        $this->requestHeader();
    }

    /**
     * exit if options and provide possible requests
     */
    private function requestMethod()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            Event::dispatch('Core\\Api::incoming', $_SERVER);
            $this->header['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                header("Content-Type: application/json");
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                    header("Access-Control-Allow-Methods: GET, POST, PUSH, DELETE, OPTIONS");
                }
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                }
                exit(0);
            }
        }
    }

    /**
     *
     */
    function requestHeader()
    {
        $endpointParts = explode('/', $_SERVER['REQUEST_URI']);
        $targetParts = explode('?', end($endpointParts));
        $target = '';
        $normalize = explode('-', $targetParts[0]);
        foreach ($normalize as $i => $part) {
            $target .= $i > 0 ? ucfirst($part) : $part;
        }
        $this->header['target'] = $target;
    }

    /**
     * @param $number
     */
    function setResponseHeader($number)
    {
        $this->responseCode = $number;
    }

    /**
     * route to class/function/data
     */
    function apiRoute()
    {
        if (!isset($this->header['target'])) {
            $this->setResponseHeader(503);
            $this->exiting(['error' => 'missing target']);
        }

        $function = strtolower($this->header['REQUEST_METHOD']) . ucfirst($this->header['target']);
        $class = '\\Neoan3\\Components\\' . $this->header['target'];
        $this->checkErrors($class, $function);
        $c = new $class(false);
        $this->setResponseHeader(200);
        try {
            if (!empty($this->stream)) {
                $responseBody = $c->$function($this->stream);
            } else {
                $responseBody = $c->$function();
            }

        } catch (RouteException $e) {
            $this->setResponseHeader($e->getCode());
            $responseBody = ['error' => $e->getMessage()];
        }
        $this->exiting($responseBody);

    }

    /**
     * @param $class
     * @param $function
     */
    private function checkErrors($class, $function)
    {
        $file = path . '/component/' . $this->header['target'] . '/' . ucfirst($this->header['target']) . '.ctrl.php';
        try {
            if (!file_exists($file)) {
                Event::dispatch('Core\\Api::error', ['msg' => 'unknown endpoint']);
                $this->setResponseHeader(404);
                throw new \Exception('unknown endpoint');
            } else {
                require_once($file);
            }
            if (!method_exists($class, $function)) {
                Event::dispatch('Core\\Api::error', ['msg' => 'method not supported']);
                $this->setResponseHeader(405);
                throw new \Exception('method ' . $this->header['REQUEST_METHOD'] . ' is not supported at this endpoint');
            }
            $r = new \ReflectionMethod($class, $function);
            $params = $r->getParameters();
            if (isset($params[0]) && !$params[0]->isOptional() && empty($this->stream)) {
                Event::dispatch('Core\\Api::error', ['msg' => 'request is empty']);
                $this->setResponseHeader(400);
                throw new \Exception('request is empty');
            }
        } catch (\Exception $e) {

            $this->exiting(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param $answer
     */
    private function exiting($answer)
    {
        Event::dispatch('Core\\Api::beforeAnswer', ['answer' => $answer, 'responseCode' => $this->responseCode]);
        http_response_code($this->responseCode);
        header('Content-type: application/json');
        echo json_encode($answer);
        exit();
    }
}
