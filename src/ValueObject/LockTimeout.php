<?php

declare(strict_types = 1);

namespace chabior\Lock\ValueObject;

class LockTimeout
{
    /**
     * @var int
     */
    private $timeout;

    private function __construct(int $timeout)
    {
        if ($timeout < 0) {
            throw new \InvalidArgumentException(sprintf('Timeout cant be lower than 0, %s given', $timeout));
        }

        $this->timeout = $timeout;
    }

    public static function fromSeconds(int $timeout): LockTimeout
    {
        return new self($timeout * 1000);
    }

    public static function fromMiliSeconds(int $timeout): LockTimeout
    {
        return new self($timeout);
    }

    public function asSeconds(): int
    {
        return (int)ceil($this->timeout / 1000);
    }
}
