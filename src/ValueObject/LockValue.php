<?php

declare(strict_types = 1);

namespace chabior\Lock\ValueObject;

class LockValue
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromRandomValue(): LockValue
    {
        return new static(sha1(microtime()));
    }

    public function equals(LockValue $value): bool
    {
        return $this->value === $value->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
