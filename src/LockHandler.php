<?php

declare(strict_types = 1);

namespace chabior\Lock;

use chabior\Lock\ValueObject\LockName;

class LockHandler
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var callable
     */
    private $successHandler;

    /**
     * @var callable
     */
    private $failHandler;

    public function __construct(StorageInterface $storage, callable  $successHandler, callable $failHandler)
    {
        $this->storage = $storage;
        $this->successHandler = $successHandler;
        $this->failHandler = $failHandler;
    }

    public function acquire(LockName $lockName): void
    {
        try {
            $this->storage->acquire($lockName);
        } catch (\Throwable $exception) {
            $this->failHandler->__invoke($this);
            return;
        }

        $this->successHandler->__invoke($this);
    }

    public function release(LockName $lockName): void
    {
        $this->storage->release($lockName);
    }
}
