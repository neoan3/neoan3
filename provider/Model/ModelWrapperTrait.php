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
    /**
     * @throws \Exception
     */
    public static function retrieveOne($input): self
    {
        $instance = new self();
        $instance->databaseTransactionMode = 'update';
        if(is_array($input)){
            $fromDatabase = self::find($input,['limit'=>[0,1]]);
            if(!empty($fromDatabase)){
                $fromDatabase = $fromDatabase[0];
            }
        } else {
            $fromDatabase = self::get($input);
        }
        if(empty($fromDatabase)){
            throw new \Exception('no entry found', 404);
        }
        $instance->generate($fromDatabase);
        return $instance;
    }

    /**
     * @throws \Exception
     */
    public function rehydrate(): ModelWrapper
    {
        if(!$this->id){
            throw new \Exception('Does not exist',404);
        }

        $this->generate(self::get($this->id));
        return $this;
    }

    public static function retrieveMany(array $input, $callFunctions = []): Collection
    {
        $instances = new Collection();
        foreach (self::find($input, $callFunctions) as $i => $result){
            $single = new self();
            $single->databaseTransactionMode = 'update';
            $single->generate($result);
            $instances->add($single);
        }

        return $instances;
    }
    public function toArray(): array
    {
        $properties =  get_object_vars($this);
        unset($properties['databaseTransactionMode']);
        return $properties;
    }
    /**
     * @throws \Exception
     */
    public function store(?string $transactionMode = null): ModelWrapper
    {
        $transactionMode = $transactionMode ?? $this->databaseTransactionMode;
        if (!method_exists(self::class, $transactionMode) && !method_exists(Transform::class, $transactionMode)) {
            throw new \Exception("Method `$transactionMode` does not exist for this model.", 500);
        }
        $this->generate(self::$transactionMode(self::toArray()));
        return $this;
    }
}
