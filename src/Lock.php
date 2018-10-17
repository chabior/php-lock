<?php

declare(strict_types = 1);

namespace chabior\Lock;

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

    public function when(callable $successHandler, callable $failHandler): LockHandler
    {
        return new LockHandler($this->storage, $successHandler, $failHandler);
    }
}
