<?php

namespace Neoan3\Core;

require_once (dirname(dirname(__DIR__)) . '/vendor/autoload.php');
require_once(dirname(__FILE__) . '/_includes.php');
new Route();
new FileServe($_GET['action']);

use Neoan3\Apps\Template;

class FileServe
{
    private array $supported;
    private array $substitutes = [];

    function __construct($action)
    {
        $this->setSupported();
        $this->substitutes['base'] = base;
        foreach ($_GET as $key => $value) {
            $this->substitutes[$key] = $value;
        }
        $parts = explode('/', $action);

        $folder = path . '/component/' . $parts[0];
        // important: file-serve requires custom delimiter in file-name
        if (isset($parts[1])) {
            foreach ($this->supported as $type) {
                $this->setSubstitutes($folder . '/' . $parts[0] . '.' . $parts[1] . '.' . $type, $type);
            }
            $keys = array_keys($this->substitutes);
            $this->mimeType(end($keys));
            echo Template::embrace(end($this->substitutes), $this->substitutes);
            exit();
        }
        echo '';
        exit();
    }

    private function mimeType($ext)
    {
        switch ($ext) {
            case 'js':
                $type = 'text/javascript';
                break;
            case 'json':
                $type = 'application/json';
                break;
            default:
                $type = 'text/' . $ext;
        }
        header('Content-Type: ' . $type);
    }

    private function setSubstitutes($path, $type)
    {
        if (file_exists($path)) {
            $this->substitutes[$type] = file_get_contents($path);
        }
    }

    private function setSupported()
    {
        $this->supported = [
            'html',
            'css',
            'js'
        ];
    }

}
