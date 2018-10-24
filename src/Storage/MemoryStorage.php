<?php

declare(strict_types = 1);

namespace chabior\Lock\Storage;

use chabior\Lock\Exception\LockException;
use chabior\Lock\StorageInterface;
use chabior\Lock\ValueObject\LockName;
use chabior\Lock\ValueObject\LockTimeout;
use chabior\Lock\ValueObject\LockValid;
use chabior\Lock\ValueObject\LockValue;

class MemoryStorage implements StorageInterface
{
    /**
     * @var LockValid[]
     */
    private $locks = [];

    public function acquire(LockName $lockName, ?LockTimeout $lockTimeout, LockValue $lockValue): void
    {
        if ($this->isLocked($lockName, $lockValue)) {
            throw new LockException();
        }

        $this->locks[$lockName->getName()] = new LockValid($lockValue, $lockTimeout);
    }

    public function release(LockName $lockName, LockValue $lockValue): void
    {
        if (isset($this->locks[$lockName->getName()]) && $this->locks[$lockName->getName()]->isValueValid($lockValue)) {
            unset($this->locks[$lockName->getName()]);
        }
    }

    public function isLocked(LockName $lockName, LockValue $lockValue): bool
    {
        return
            isset($this->locks[$lockName->getName()]) &&
            $this->locks[$lockName->getName()]->isValid($lockValue)
        ;
    }
}
