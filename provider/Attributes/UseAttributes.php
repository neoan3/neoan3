<?php


namespace Neoan3\Provider\Attributes;


use Neoan3\Core\Event;
use Neoan3\Provider\Auth\AuthObjectDeclaration;
use Neoan3\Provider\Auth\Authorization;

class UseAttributes
{
    public ?AuthObjectDeclaration $authObject = null;

    function hookAttributes($provider)
    {
        Event::hook('Core\\Reflection::attribute', function ($payload) use ($provider) {
            if ($payload['name'] == Authorization::class) {
                $payload['instance']->execute($provider, $this->authObject);
            } else {
                $payload['instance']->execute($provider);
            }
        });
    }
}