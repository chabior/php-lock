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

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function acquire(LockName $lockName): Promise
    {
        return new Promise(function () use($lockName) {
            $this->storage->acquire($lockName);
        });
    }

    public function release(LockName $lockName): Promise
    {
        return new Promise(function () use($lockName) {
            $this->storage->release($lockName);
        });
    }
}
