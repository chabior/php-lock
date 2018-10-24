<?php

declare(strict_types = 1);

namespace chabior\Lock\ValueObject;

class LockValid
{
    /**
     * @var LockTimeout
     */
    private $timeout;

    /**
     * @var \DateTimeImmutable
     */
    private $startDate;

    /**
     * @var LockValue
     */
    private $lockValue;

    public function __construct(LockValue $lockValue, ?LockTimeout $lockTimeout)
    {
        $this->timeout = $lockTimeout;
        $this->startDate = new \DateTimeImmutable();
        $this->lockValue = $lockValue;
    }

    public function isValid(LockValue $lockValue): bool
    {
        if (!$this->isValueValid($lockValue)) {
            return false;
        }

        if (!$this->timeout) {
            return true;
        }

        return $this->startDate->modify(sprintf('+ %d seconds', $this->timeout->asSeconds())) > new \DateTimeImmutable();
    }

    public function isValueValid(LockValue $lockValue): bool
    {
        return $lockValue->equals($this->lockValue);
    }
}
