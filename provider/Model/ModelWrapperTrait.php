<?php

namespace Neoan3\Provider\Model;

use Neoan3\Provider\MySql\Transform;

trait ModelWrapperTrait{
    private string $databaseTransactionMode = 'create';
    function __construct(array $generate = null)
    {
        if ($generate) {
            $this->generate($generate);
        }
        return $this;
    }

    private function generate($staticModel)
    {
        foreach ($staticModel as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public static function retrieve(mixed $input)
    {
        $instance = new self();
        $instance->databaseTransactionMode = 'update';
        if(is_array($input)){
            $instance->generate(self::find($input)[0]);
        } else {
            $instance->generate(self::get($input));
        }
        return $instance;
    }
    public function toArray(): array
    {
        return get_object_vars($this);
    }
    /**
     * @throws \Exception
     */
    public function store(?string $transactionMode = null): self
    {
        $transactionMode = $transactionMode ?? $this->databaseTransactionMode;
        if (!method_exists(self::class, $transactionMode) && !method_exists(Transform::class, $transactionMode)) {
            throw new \Exception("Method `$transactionMode` does not exist for this model.", 500);
        }
        $this->generate(self::$transactionMode(get_object_vars($this)));
        return $this;
    }
}
