<?php

namespace Neoan3\Provider\Model;

use Iterator;

class Collection implements Iterator
{
    private int $position;

    private array $modelInstances = [];

    public function __construct() {
        $this->position = 0;
    }

    function map(callable $callback)
    {
        foreach ($this->modelInstances as $modelInstance){
            $callback($modelInstance);
        }
        return $this;
    }

    function add(ModelWrapper $modelInstance): self
    {
        $this->modelInstances[] = $modelInstance;
        return $this;
    }
    function toArray(): array
    {
        $output = [];
        foreach ($this->modelInstances as $modelInstance){
            $output[] = $modelInstance->toArray();
        }
        return $output;
    }
    function store(): self
    {
        foreach ($this->modelInstances as $modelInstance){
            $modelInstance->store();
        }
        return $this;
    }
    function count(): int
    {
        return count($this->modelInstances);
    }

    public function current()
    {
        return $this->modelInstances[$this->position];
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->modelInstances[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }
}