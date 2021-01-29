<?php


namespace Neoan3\Core;


use ReflectionMethod;

class ReflectionWrapper
{
    public ReflectionMethod $method;
    function __construct($class, $function = null)
    {
        $this->method = new ReflectionMethod($class, $function);
    }

    function dispatchAttributes(string $sender): array
    {
        $return = [];
        if(PHP_MAJOR_VERSION >= 8){
            $attrs = $this->method->getAttributes();
            if(count($attrs)>0){
                foreach ($attrs as $attr){
                    $eventPayload = [
                        'instance' => $attr->newInstance(),
                        'target' => $attr->getTarget(),
                        'name' => $attr->getName(),
                        'arguments' => [...$attr->getArguments()],
                        'sender' => $sender
                    ];

                    $return[] = $eventPayload;
                    Event::dispatch('Core\\Reflection::attribute', $eventPayload);
                }
            }
        }

        return $return;
    }
}