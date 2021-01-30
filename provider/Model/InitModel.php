<?php


namespace Neoan3\Provider\Model;

use Attribute;
use Neoan3\Provider\Attributes\AttributeReaction;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_ALL)]
class InitModel extends AttributeReaction
{
    public string $modelClass;
    public function __construct($modelClass)
    {
        $this->modelClass = $modelClass;
    }
    public function execute($provider)
    {
        $this->modelClass::init($provider);
    }
}