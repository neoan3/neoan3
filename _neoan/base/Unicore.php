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
     * @var array
     */
    public array $injections = [];

    /**
     * @var Serve
     */
    public Serve $uniCore;

    /**
     * @param ?string $frame
     * @return Serve
     */
    function uni($frame = null)
    {
        if ($frame) {
            $class = '\\Neoan3\\Frame\\' . ucfirst($frame);
            $this->uniCore = new $class(...$this->injections);
        } else {
            $this->uniCore = new Serve();
        }

        $track = debug_backtrace();
        $this->uniCore->renderer->setComponentName($track[1]['class']);
        return $this->uniCore;
    }

    /**
     * @param $provider
     * @return $this
     */
    public function registerProvider($provider)
    {
        $this->injections[] = $provider;
        return $this;
    }

}