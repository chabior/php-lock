<?php

declare(strict_types = 1);

namespace chabior\Lock;

use chabior\Lock\ValueObject\LockName;
use chabior\Lock\ValueObject\LockTimeout;

interface StorageInterface
{
    public function acquire(LockName $lockName, LockTimeout $lockTimeout): void;

    public function release(LockName $lockName): void;

    public function isLocked(LockName $lockName): bool;
}
