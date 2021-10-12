<?php

namespace Neoan3\Provider\Model;

interface ModelWrapper
{
    public function toArray(): array;
    public function store(?string $transactionMode = null): ModelWrapper;
    public function rehydrate(): ModelWrapper;
}