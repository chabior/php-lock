<?php

declare(strict_types = 1);

namespace chabior\Lock;

class Promise
{
    /**
     * @var callable
     */
    private $successHandler;

    /**
     * @var callable
     */
    private $failHandler;

    /**
     * @var callable
     */
    private $executor;

    public function __construct(callable $executor)
    {
        $this->executor = $executor;
    }

    public function then(callable $successHandler, callable $failHandler): Promise
    {
        $promise = clone $this;
        $promise->successHandler = $successHandler;
        $promise->failHandler = $failHandler;

        return $promise;
    }

    public function resolve(): void
    {
        try {
            $this->executor->__invoke();
        } catch (\Throwable $exception) {
            $this->failHandler->__invoke();
            return;
        }

        $this->successHandler->__invoke();
    }
}
