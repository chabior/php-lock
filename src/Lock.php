<?php

declare(strict_types = 1);

namespace chabior\Lock;

use chabior\Lock\ValueObject\LockName;

class Lock
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var callable
     */
    private $successHandler;

    /**
     * @var callable
     */
    private $failHandler;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function success(callable $successHandler): Lock
    {
        $lock = clone $this;
        $lock->successHandler = $successHandler;
        return $lock;
    }

    public function fail(callable $failHandler): Lock
    {
        $lock = clone $this;
        $lock->failHandler = $failHandler;
        return $lock;
    }

    public function acquire(LockName $lockName): void
    {
        if ($this->successHandler === null) {
            throw new \RuntimeException('Success handler is required');
        }

        if ($this->failHandler === null) {
            throw new \RuntimeException('Fail handler is required');
        }

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
