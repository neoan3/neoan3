<?php

namespace Neoan3\Core;

class Route
{
    public $call;
    public array $url_parts;
    public string $protocol;

    function __construct()
    {
        require_once(path . '/default.php');
        $this->protocol = ($_SERVER['SERVER_PORT'] != '80' && empty($_SERVER['HTTPS']) ? ':' . $_SERVER['SERVER_PORT'] : '');
        $this->defineBase();
        $this->call = ucfirst(default_ctrl);
        $this->loader();
    }

    private function defineBase()
    {
        $string = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http');
        $string .= '://' . $_SERVER['SERVER_NAME'] . $this->protocol;
        preg_match('/.+?(?=index\.php)/', $_SERVER['PHP_SELF'], $matches);
        $string .= $matches[0] ?? '';
        $string = str_replace(['\\','_neoan/base','_neoan/api'],['','',''],$string);

        if (substr($string, -2) === '//') {
            $string .= substr($string, -1);
        } elseif (substr($string, -1) !== '/') {
            $string .= '/';
        }
        define('base', $string);
    }

    private function loader()
    {
        if (isset($_GET['action']) && trim($_GET['action']) != '') {
            $this->url_parts = explode('/', $_GET['action']);
            $normalize = explode('-', $this->url_parts[0]);
            $this->call = '';
            foreach ($normalize as $i => $part) {
                $this->call .= ucfirst(strtolower($part));
            }
        }

        if (!file_exists(path . '/component/' . $this->call . '/' . $this->call . 'Controller.php')) {
            Event::dispatch('Core\\Route::notFound', ['component' => $this->call]);
            require_once(neoan_path . '/base/error_404.core.php');
            $this->call = 'Error_404';
        }
        define('current_endpoint', $this->call);
    }
}
