<?php

declare(strict_types = 1);

namespace chabior\Lock\Tests\Dummy;

use chabior\Lock\StorageInterface;
use chabior\Lock\ValueObject\LockName;

class FailStorage implements StorageInterface
{

    public function acquire(LockName $lockName): void
    {
        throw new \RuntimeException(sprintf('Failed to acquire lock %s', $lockName->getName()));
    }

    public function release(LockName $lockName): void
    {
        throw new \RuntimeException(sprintf('Failed to release lock %s', $lockName->getName()));
    }
}
