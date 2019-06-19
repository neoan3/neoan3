<?php

class Route{
    public $call;
    public $url_parts;
    public $protocol;
    function __construct() {
        require_once(path.'/default.php');
        $this->protocol = ($_SERVER['SERVER_PORT']!='80'&&empty($_SERVER['HTTPS'])?':8080':'');
        $this->defineBase();
        $this->call = default_ctrl;
        $this->loader();
    }
    private function defineBase(){
        $string = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'? 'https':'http');
        $string .= '://' . $_SERVER['SERVER_NAME'] . $this->protocol;
        $string .= dirname($_SERVER['PHP_SELF']);
        $string = str_replace('\\','',$string);

        if(substr($string,-2) === '//'){
            $string .= substr($string,-1);
        } elseif(substr($string,-1) !== '/'){
            $string .= '/';
        }
        define('base', $string );
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
