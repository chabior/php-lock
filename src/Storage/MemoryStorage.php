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
     * @var array
     */
    private $isLocked = [];

    public function acquire(LockName $lockName, ?LockTimeout $lockTimeout, LockValue $lockValue): void
    {
        if ($this->isLocked($lockName, $lockValue)) {
            throw new LockException();
        }

        $this->isLocked[$lockName->getName()] = [
            'timeout' => new LockValid($lockTimeout),
            'value' => $lockValue,
        ];
    }

    public function release(LockName $lockName, LockValue $lockValue): void
    {
        if (isset($this->isLocked[$lockName->getName()]) && $lockValue->equals($this->isLocked[$lockName->getName()]['value'])) {
            unset($this->isLocked[$lockName->getName()]);
        }
    }

    public function isLocked(LockName $lockName, LockValue $lockValue): bool
    {
        return
            isset($this->isLocked[$lockName->getName()]) &&
            $this->isLocked[$lockName->getName()]['timeout']->isValid() &&
            $lockValue->equals($this->isLocked[$lockName->getName()]['value'])
        ;
    }
}
