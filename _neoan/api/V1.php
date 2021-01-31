<?php

namespace Neoan3\Api;

use Exception;
use Neoan3\Core\Event;
use Neoan3\Core\ReflectionWrapper;
use Neoan3\Core\RouteException;
use ReflectionMethod;

/**
 * Class Api
 */
class V1
{
    /**
     * @var
     */
    public $stream;
    /**
     * @var array
     */
    public array $header = [];
    /**
     * @var int
     */
    private int $responseCode;

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
     * @param $part
     * @return string
     */
    function normalize($part)
    {
        $target = '';
        $normalize = explode('-', $part);
        foreach ($normalize as $i => $part) {
            $target .= ucfirst($part);
        }
        return $target;
    }

    /**
     * Identify target
     */
    function requestHeader()
    {
        $cleanRequest = $_SERVER['REQUEST_URI'];
        if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
            $cleanRequest = mb_substr($_SERVER['REQUEST_URI'], 0, (mb_strlen($_SERVER['QUERY_STRING']) + 1) * -1);
        }
        $endpointParts = explode('/', $cleanRequest);
        if (!isset($this->header['arguments'])) {
            $this->header['arguments'] = [];
        }
        $next = false;
        $function = false;
        foreach ($endpointParts as $part) {
            if ($next && !$function) {
                $function = $this->normalize($part);
            } elseif ($next) {
                $this->header['arguments'][] = $this->normalize($part);
            }
            if ($part == 'api.v1') {
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
        $this->responseCode = (int)$number;
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
        $class = '\\Neoan3\\Component\\' . $this->header['target'] . '\\' . $this->header['target'] . 'Controller';
        $this->checkErrors($class, $function);
        try {
            $c = new $class();
            $this->setResponseHeader(200);
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

    /**
     * @param $class
     * @param $function
     */
    private function checkErrors($class, $function)
    {
        try {
            if (!class_exists($class)) {
                Event::dispatch('Core\\Api::error', ['msg' => 'unknown endpoint']);
                $this->setResponseHeader(404);
                throw new Exception('unknown endpoint');
            } elseif (!method_exists($class, $function)) {
                Event::dispatch('Core\\Api::error', ['msg' => 'method not supported']);
                $this->setResponseHeader(405);
                throw new Exception('method ' . $this->header['REQUEST_METHOD'] . ' is not supported at this endpoint');
            }
            $r = new ReflectionWrapper($class, $function);
            $r->dispatchAttributes(__NAMESPACE__);
            $params = $r->method->getParameters();
            $totalParams = count($params);
            $lastParam = array_pop($params);
            // last: body/params
            if ($lastParam && !$lastParam->isDefaultValueAvailable() && (empty($this->stream) && count($this->header['arguments']) < $totalParams )) {
                Event::dispatch('Core\\Api::error', ['msg' => 'request is empty']);
                $this->setResponseHeader(400);
                throw new Exception('request is empty');
            }
            // other (arguments)
            foreach ($params as $i => $param) {
                if (!isset($this->header['arguments'][$i])) {
                    if ($param->isDefaultValueAvailable()) {
                        $this->header['arguments'][$i] = $param->getDefaultValue();
                    } else {
                        Event::dispatch('Core\\Api::error', ['msg' => 'missing argument: ' . $param->getName()]);
                        $this->setResponseHeader(400);
                        throw new Exception('missing argument: ' . $param->getName());
                    }

                }
            }
        } catch (Exception $e) {
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
        Event::dispatch('Core\\Api::afterAnswer', ['answer' => $answer, 'responseCode' => $this->responseCode]);
        exit();
    }
}
