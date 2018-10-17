<?php

declare(strict_types = 1);

namespace chabior\Lock\Storage;

use chabior\Lock\StorageInterface;
use chabior\Lock\ValueObject\LockName;

class MemoryStorage implements StorageInterface
{
    private $isLocked = [];

    public function acquire(LockName $lockName): void
    {
        $this->isLocked[$lockName->getName()] = true;
    }

    public function release(LockName $lockName): void
    {
        unset($this->isLocked[$lockName->getName()]);
    }

    public function isLocked(LockName $lockName): bool
    {
        return isset($this->isLocked[$lockName->getName()]) && $this->isLocked[$lockName->getName()] === true;
    }
}
