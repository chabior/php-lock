<?php

declare(strict_types = 1);

namespace chabior\Lock\Tests;

use chabior\Lock\Lock;
use chabior\Lock\Storage\MemoryStorage;
use chabior\Lock\Tests\Dummy\FailStorage;
use chabior\Lock\ValueObject\LockName;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;

class LockTest extends TestCase
{
    public function testLock()
    {
        $storage = new MemoryStorage();
        $name = new LockName('silly');
        $lock = new Lock($storage);
        $lock
            ->success(function () use ($name, $storage) {
                $this::assertTrue($storage->isLocked($name));
            })
            ->fail(function () {
                throw new AssertionFailedError('Success lock handler called');
            })
            ->acquire($name)
        ;
    }

    public function testFailLock()
    {
        $name = new LockName('silly');
        $lock = new Lock(new FailStorage());
        $lock
            ->success(function () {
                throw new AssertionFailedError('Fail lock handler called');
            })
            ->fail(function () {
                $this::assertTrue(true);
            })
            ->acquire($name)
        ;
    }

    public function testReleaseLock()
    {
        $storage = new MemoryStorage();
        $name = new LockName('silly');
        $lock = new Lock($storage);
        $lock
            ->success(function (Lock $lock) use($name, $storage) {
                $this::assertTrue($storage->isLocked($name));
                $lock->release($name);
                $this::assertFalse($storage->isLocked($name));
            })
            ->fail(function () {
                throw new AssertionFailedError('Success lock handler called');
            })
            ->acquire($name)
        ;
    }
}
