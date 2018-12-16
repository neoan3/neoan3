<?php

require_once(dirname(__FILE__) . '/_includes.php');
$route = new Route('api');
$api = new Api();
$api->apiRoute();
exit();

/**
 * Class Api
 */
class Api {
    /**
     * @var
     */
    public $post;

    /**
     * Api constructor.
     */
    function __construct() {
        $data = file_get_contents('php://input');
        if(!empty($data)){
            $divide = json_decode($data, true);
            // secure here!!
            $this->post = $divide;
        } elseif(!empty($_FILES)){
            $this->post = $_POST;
        }
        $this->requestMethod();
    }

    /**
     * exit if options
     */
    private function requestMethod(){
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header("Content-Type: application/json");
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            exit(0);
        }
    }

    /**
     * route to class/function/data
     */
    function apiRoute(){

        $frame = $this->post['config'];
        require_once(path . '/frame/' . $frame . '/' . $frame . '.php');
        $frame = new Neoan3\Frame\Frame();
        require_once(path . '/component/' . $this->post['c'] . '/' . $this->post['c'] . '.ctrl.php');
        $class = __NAMESPACE__ . '\\Neoan3\\Components\\'.$this->post['c'];
        $c = new $class(false);

        $function = $this->post['f'];
        $obj = (isset($this->post['d']) ? $this->post['d'] : array());
        header('Content-type: application/json');
        if (!empty($obj)) {
            echo json_encode($c->$function($obj));
        } else {
            echo json_encode($c->$function());
        }
    }
}
