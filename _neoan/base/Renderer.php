<?php


namespace Neoan3\Core;



interface Renderer
{
    public function output($afterHooks = []): void;
    public function setComponentName($qualifiedClassName): void;
    public function getComponentName(): string;
    public function setLang(string $lang): void;
    public function setTitle(string $title): void;
    public function attachParameters($assocArray=[]): void;
    public function assignToHook($name, $view, $params = []): void;
}