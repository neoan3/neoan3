<?php


namespace Neoan3\Provider\Auth;

use Attribute;
use Neoan3\Provider\Attributes\AttributeReaction;

#[Attribute]
class Authorization extends AttributeReaction
{
    public string $notation;
    public array $scope;
    public function __construct(string $notation, array $scope = [])
    {
        $this->notation = $notation;
        $this->scope = $scope;
    }
    public function execute($provider, &$returnVariable)
    {
        $returnVariable = $provider['auth']->{$this->notation}($this->scope);
    }
}