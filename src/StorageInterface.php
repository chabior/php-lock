<?php

declare(strict_types = 1);

namespace chabior\Lock;

use chabior\Lock\ValueObject\LockName;
use chabior\Lock\ValueObject\LockTimeout;
use chabior\Lock\ValueObject\LockValue;

interface StorageInterface
{
    public function acquire(LockName $lockName, LockTimeout $lockTimeout, LockValue $lockValue): void;

    public function release(LockName $lockName, LockValue $lockValue): void;

    public function isLocked(LockName $lockName, LockValue $lockValue): bool;
}
