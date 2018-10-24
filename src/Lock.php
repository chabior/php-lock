<?php

declare(strict_types = 1);

namespace chabior\Lock;

use chabior\Lock\Exception\LockException;
use chabior\Lock\Handler\HandlerInterface;
use chabior\Lock\ValueObject\LockName;
use chabior\Lock\ValueObject\LockTimeout;
use chabior\Lock\ValueObject\LockValue;

class Lock
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var HandlerInterface
     */
    private $successHandler;

    /**
     * @var HandlerInterface
     */
    private $failHandler;

    /**
     * @var LockTimeout
     */
    private $lockTimeout;

    /**
     * @var LockValue
     */
    private $lockValue;

    public function __construct(StorageInterface $storage, LockTimeout $lockTimeout = null, LockValue $lockValue = null)
    {
        $this->storage = $storage;
        $this->lockTimeout = $lockTimeout;
        $this->lockValue = $lockValue ?? LockValue::fromRandomValue();
    }

    public function success(HandlerInterface $successHandler): Lock
    {
        $lock = clone $this;
        $lock->successHandler = $successHandler;
        return $lock;
    }

    public function fail(HandlerInterface $failHandler): Lock
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
            $this->storage->acquire($lockName, $this->lockTimeout, $this->lockValue);
        } catch (\Throwable $exception) {
            $this->failHandler->handle($this);
            return;
        }

        $this->successHandler->handle($this);
    }

    public function release(LockName $lockName): void
    {
        $this->storage->release($lockName, $this->lockValue);
    }

    public function isLocked(LockName $lockName): bool
    {
        return $this->storage->isLocked($lockName, $this->lockValue);
    }
}
