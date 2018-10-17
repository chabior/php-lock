<?php

declare(strict_types = 1);

namespace chabior\Lock\Storage;

use chabior\Lock\Exception\LockException;
use chabior\Lock\StorageInterface;
use chabior\Lock\ValueObject\LockName;
use chabior\Lock\ValueObject\LockTimeout;
use chabior\Lock\ValueObject\LockValid;

class MemoryStorage implements StorageInterface
{
    /**
     * @var LockValid[]
     */
    private $isLocked = [];

    public function acquire(LockName $lockName, ?LockTimeout $lockTimeout): void
    {
        if ($this->isLocked($lockName)) {
            throw new LockException();
        }

        $this->isLocked[$lockName->getName()] = new LockValid($lockTimeout);
    }

    public function release(LockName $lockName): void
    {
        unset($this->isLocked[$lockName->getName()]);
    }

    public function isLocked(LockName $lockName): bool
    {
        return isset($this->isLocked[$lockName->getName()]) && $this->isLocked[$lockName->getName()]->isValid();
    }
}
