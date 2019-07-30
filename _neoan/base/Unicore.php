<?php
/**
 * Created by PhpStorm.
 * User: Stefan
 * Date: 12/9/2018
 * Time: 1:33 PM
 */

namespace Neoan3\Core;

/**
 * Class Unicore
 * @package Neoan3\Core
 */
class Unicore
{
    /**
     * @var
     */
    public $uniCore;

    /**
     * @param string $frame
     * @return Serve
     */
    function uni($frame = '')
    {
        if ($frame != '') {
            $class = '\\Neoan3\\Frame\\' . ucfirst($frame);
            $this->uniCore = new $class();
        } else {
            $this->uniCore = new Serve();
        }

        $track = debug_backtrace();
        $this->setRunComponent($track[0]['file']);
        return $this->uniCore;
    }

    /**
     * @param $file
     */
    function setRunComponent($file)
    {
        $folder = substr($file, 0, strrpos($file, DIRECTORY_SEPARATOR));
        $fParts = explode(DIRECTORY_SEPARATOR, $folder);
        $component = end($fParts);
        $this->uniCore->runComponent = [
            $folder,
            $component
        ];
    }

}