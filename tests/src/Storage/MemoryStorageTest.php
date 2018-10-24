<?php

declare(strict_types = 1);

namespace chabior\Lock\Tests\Storage;

use chabior\Lock\Storage\MemoryStorage;
use chabior\Lock\ValueObject\LockName;
use chabior\Lock\ValueObject\LockTimeout;
use chabior\Lock\ValueObject\LockValue;
use PHPUnit\Framework\TestCase;

class MemoryStorageTest extends TestCase
{
    public function testAcquireWithoutTimeout(): void
    {
        $name = new LockName('test');
        $value = LockValue::fromRandomValue();
        $storage = new MemoryStorage();
        $storage->acquire($name, null, $value);

        $this::assertTrue($storage->isLocked($name, $value));
    }

    public function testAcquireWithTimeout(): void
    {
        $name = new LockName('test');
        $value = LockValue::fromRandomValue();
        $storage = new MemoryStorage();
        $storage->acquire($name, LockTimeout::fromSeconds(5), $value);

        $this::assertTrue($storage->isLocked($name, $value));
    }

    public function testAcquireWithTimeoutExpired(): void
    {
        $name = new LockName('test');
        $value = LockValue::fromRandomValue();
        $storage = new MemoryStorage();
        $storage->acquire($name, LockTimeout::fromSeconds(1), $value);

        $this::assertTrue($storage->isLocked($name, $value));
        sleep(1);
        $this::assertFalse($storage->isLocked($name, $value));
    }

    public function testRelease(): void
    {
        $name = new LockName('test');
        $value = LockValue::fromRandomValue();
        $storage = new MemoryStorage();
        $storage->acquire($name, null, $value);
        $storage->release($name, $value);

        $this::assertFalse($storage->isLocked($name, $value));
    }

    public function testReleaseWithDifferentName(): void
    {
        $name = new LockName('test');
        $value = LockValue::fromRandomValue();
        $storage = new MemoryStorage();
        $storage->acquire($name, null, $value);
        $storage->release($name, LockValue::fromRandomValue());

        $this::assertTrue($storage->isLocked($name, $value));
    }

    public function testIsLocked(): void
    {
        $name = new LockName('test');
        $value = LockValue::fromRandomValue();
        $storage = new MemoryStorage();
        $this::assertFalse($storage->isLocked($name, $value));
    }

    public function testIsLockedWithDifferentName(): void
    {
        $name = new LockName('test');
        $value = LockValue::fromRandomValue();
        $storage = new MemoryStorage();
        $storage->acquire(new LockName('asd'), null, $value);
        $this::assertFalse($storage->isLocked($name, $value));
    }

    public function testIsLockedWIthDifferentValue(): void
    {
        $name = new LockName('test');
        $value = LockValue::fromRandomValue();
        $storage = new MemoryStorage();
        $storage->acquire($name, null, $value);
        $this::assertFalse($storage->isLocked($name, LockValue::fromRandomValue()));
        $this::assertTrue($storage->isLocked($name, $value));
    }
}
