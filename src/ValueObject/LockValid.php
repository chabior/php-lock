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

    public function __construct(?LockTimeout $lockTimeout)
    {
        $this->timeout = $lockTimeout;
        $this->startDate = new \DateTimeImmutable();
    }

    public function isValid(): bool
    {
        if (!$this->timeout) {
            return true;
        }

        return $this->startDate->modify(sprintf('+ %d seconds', $this->timeout->asSeconds())) > new \DateTimeImmutable();
    }
}
