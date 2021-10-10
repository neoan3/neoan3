<?php

namespace Neoan3\Core;


use Neoan3\Provider\Model\Model;

/**
 * Class Serve
 * @package Neoan3\Core
 */
class Serve
{


    /**
     * @var array
     */
    public array $provider = [];
    /**
     * @var Renderer
     */
    public Renderer $renderer;

    /**
     * Serve constructor.
     * @param Renderer|null $renderer
     */
    public function __construct(Renderer $renderer = null)
    {
        if($renderer){
            $this->renderer = new $renderer($this->constants());
        } else {
            $this->renderer = new Render($this->constants());
        }
    }

    /**
     * @param $name
     * @param $provided
     * @param $callback
     * @return mixed
     */
    public function assignProvider($name, $provided, $callback)
    {
        if($provided){
            $this->provider[$name] = $provided;
        } elseif($return = $callback()) {
            $this->provider[$name] = $return;
        }
        return $this->provider[$name] ?? null;
    }

    /**
     * @param $as
     * @param $callback
     * @return $this
     */
    public function addRenderParameter($as, $callback): Serve
    {
        $params = [$as => $callback($this)];
        $this->renderer->attachParameters($params);
        return $this;
    }

    /**
     * @param $modelClass
     * @return Model
     */
    public function loadModel($modelClass)
    {
        $modelClass::init($this->provider);
        return $modelClass;
    }


    /**
     * @return array
     */
    public function constants()
    {
        return [];
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->renderer->setTitle($title);
        return $this;
    }

    /**
     * @param string $lang
     * @return $this
     */
    public function setLang(string $lang)
    {
        $this->renderer->setLang($lang);
        return $this;
    }

    /**
     * @param $hook
     * @param $view
     * @param array $params
     * @return $this
     */
    function hook($hook, $view, $params = [])
    {
        $this->renderer->assignToHook($hook, $view, $params);
        return $this;
    }

    /**
     * @param $contextOrCallback
     * @param null $callback
     * @return $this
     */
    function callback($contextOrCallback, $callback = null)
    {
        if($callback){
            $contextOrCallback->$callback($this);
        } else {
            $contextOrCallback($this);
        }
        return $this;
    }

    /**
     * echos DOM from Renderer
     * @param array $params optional
     */
    function output($params = [])
    {
        $this->renderer->output($params);
    }
}
