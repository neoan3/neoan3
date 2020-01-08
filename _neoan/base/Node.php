<?php

namespace Neoan3\Core;

/*
 * JS module bare imports were not implemented across browsers at the time of release of neoan3
 * the .htaccess contains a rewrite to this file to handle such issues
 * */
require_once(dirname(__FILE__) . '/_includes.php');
$route = new Route();

if (isset($_GET['action'])) {
    header('Content-Type: application/javascript');
    $js = new Node($_GET['action']);
    echo $js->answer;
}

/**
 * Class Node
 */
class Node
{
    /**
     * @var string
     */
    public $answer;

    /**
     * Node constructor.
     *
     * @param $path
     */
    function __construct($path)
    {
        $file = path . '/node_modules/' . $path;
        if (file_exists($file)) {
            $this->answer = $this->parseFile($file);
        } else {
            $this->answer = $this->error($path);
        }

    }

    /**
     * @param $include
     *
     * @return string
     */
    function error($include)
    {
        return "console.error('neoan3 nodejs import failed ($include)');";
    }

    /**
     * @param $file
     *
     * @return string|string[]|null
     */
    function parseFile($file)
    {
        $file = file_get_contents($file);
        $base = substr(base, -1) == '/' ? base : base . '/';
        return preg_replace('/(\s[\'|\"])(@[a-z0-9\/\-\.]+)/', '$1' . $base . 'node_modules/$2', $file);

    }

}