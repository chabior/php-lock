<?php

declare(strict_types = 1);

namespace chabior\Lock\Tests\Dummy;

use chabior\Lock\StorageInterface;
use chabior\Lock\ValueObject\LockName;
use chabior\Lock\ValueObject\LockTimeout;
use chabior\Lock\ValueObject\LockValue;

class FailStorage implements StorageInterface
{

    public function acquire(LockName $lockName, LockTimeout $lockTimeout, LockValue $lockValue): void
    {
        throw new \RuntimeException(sprintf('Failed to acquire lock %s', $lockName->getName()));
    }

    public function release(LockName $lockName, LockValue $lockValue): void
    {
        throw new \RuntimeException(sprintf('Failed to release lock %s', $lockName->getName()));
    }

    public function isLocked(LockName $lockName, LockValue $lockValue): bool
    {
        return false;
    }
}
