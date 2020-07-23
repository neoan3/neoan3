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
                    header("Access-Control-Allow-Methods: GET, POST, PUSH, PUT, PATCH, DELETE, OPTIONS");
                }
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                }
                exit(0);
            }
        }
    }

    /**
     * @param $parts
     * @return string
     */
    function normalize($parts)
    {
        $target = '';
        $normalize = explode('-', $parts[0]);
        foreach ($normalize as $i => $part) {
            $target .= $i > 0 ? ucfirst($part) : $part;
        }
        return $target;
    }

    /**
     * Identify target
     */
    function requestHeader()
    {
        $endpointParts = explode('/', $_SERVER['REQUEST_URI']);
        if(!isset($this->header['arguments'])){
            $this->header['arguments'] = [];
        }
        $next = false;
        $function = false;
        foreach ($endpointParts as $part){
            if($next && !$function){
                $function = $this->normalize(explode('?', $part));
            } elseif($next) {
                $this->header['arguments'][] = $this->normalize(explode('?', $part));
            }
            if($part == 'api.v1'){
                $next = true;
            }
        }
        $this->header['target'] = $function;
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
//            $responseBody = $c->$function($this->constructParameters());
            if (!empty($this->stream)) {
                $this->header['arguments'][] = $this->stream;
                $responseBody = $c->$function(...$this->header['arguments']);
            } else {
                $responseBody = $c->$function(...$this->header['arguments']);
            }

        } catch (RouteException $e) {
            $this->setResponseHeader($e->getCode());
            $responseBody = ['error' => $e->getMessage()];
        }
        $this->exiting($responseBody);

    }
    private function constructParameters(){
        $functionParameters = [];
        foreach ($this->header['arguments'] as $argument){
            $functionParameters[] = $argument;
        }
        if (!empty($this->stream)) {
            $functionParameters[] = $this->stream;
        }
        return $functionParameters;
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
            $lastParam = array_pop($params);
            // last: body/params
            if(!$lastParam->isDefaultValueAvailable() && empty($this->stream)) {
                Event::dispatch('Core\\Api::error', ['msg' => 'request is empty']);
                $this->setResponseHeader(400);
                throw new \Exception('request is empty');
            }
            // other (arguments)
            foreach($params as $i => $param){
                if(!isset($this->header['arguments'][$i])){
                    if($param->isDefaultValueAvailable()){
                        $this->header['arguments'][$i] = $param->getDefaultValue();
                    } else {
                        Event::dispatch('Core\\Api::error', ['msg' => 'missing argument: ' . $param->getName()]);
                        $this->setResponseHeader(400);
                        throw new \Exception('missing argument: ' . $param->getName());
                    }

                }
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
