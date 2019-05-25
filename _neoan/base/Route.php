<?php

class Route{
    public $call;
    public $url_parts;
    public $protocol;
    function __construct($context='view') {
        require_once(path.'/default.php');
        $this->protocol = ($_SERVER['SERVER_PORT']!='80'&&empty($_SERVER['HTTPS'])?':8080':'');
        $this->defineBase($this->offset($context));
        $this->call = default_ctrl;
        $this->loader();
    }
    private function defineBase($offset){
        $string = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'? 'https':'http');
        $string .= '://' . $_SERVER['SERVER_NAME'] . $this->protocol;
        $string .= substr($_SERVER['PHP_SELF'],0,$offset);
        if(substr($string,-2) === '//'){
            $string .= substr($string,-1);
        } elseif(substr($string,-1) !== '/'){
            $string .= '/';
        }
        define('base', $string );
    }
    private function offset($context){
        $r = 0;
        switch ($context){
            case 'view': $r = -9-strlen($this->protocol); break;
            case 'api': $r = -19-strlen($this->protocol); break;
            case 'node': $r = -21-strlen($this->protocol); break;
            case 'fileServe': $r = -26-strlen($this->protocol); break;
        }
        return $r;
    }
    private function loader(){
        if (isset($_GET['action']) && trim($_GET['action']) != '') {
            $this->url_parts = explode('/', $_GET['action']);
            $normalize = explode('-',$this->url_parts[0]);
            $this->call = '';
            foreach($normalize as $part){
                $this->call .= strtolower(strtolower($part));
            }
        }

        $className = ucfirst($this->call);
        if(file_exists(path . '/component/' . $this->call . '/' . $className . '.ctrl.php')){
            require_once(path . '/component/' . $this->call . '/' . $className . '.ctrl.php');
        } else {
            require_once(neoan_path .'/base/error_404.core.php');
            $this->call = 'error_404';
        }
        define('current_endpoint',$this->call);
    }
}
