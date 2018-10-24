<?php

declare(strict_types = 1);

namespace chabior\Lock\Tests;

use chabior\Lock\Handler\CallbackHandler;
use chabior\Lock\Handler\FailCallbackHandler;
use chabior\Lock\Lock;
use chabior\Lock\Storage\MemoryStorage;
use chabior\Lock\Tests\Dummy\FailStorage;
use chabior\Lock\ValueObject\LockName;
use chabior\Lock\ValueObject\LockTimeout;
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
            ->success(new CallbackHandler(function (Lock $lock) use ($name) {
                $this::assertTrue($lock->isLocked($name));
            }))
            ->fail(new FailCallbackHandler(function () {
                throw new AssertionFailedError('Success lock handler called');
            }))
            ->acquire($name)
        ;
    }

    public function testFailLock()
    {
        $name = new LockName('silly');
        $lock = new Lock(new FailStorage());
        $lock
            ->success(new CallbackHandler(function () {
                throw new AssertionFailedError('Fail lock handler called');
            }))
            ->fail(new FailCallbackHandler(function () {
                $this::assertTrue(true);
            }))
            ->acquire($name)
        ;
    }

    public function testReleaseLockInSuccessHandler()
    {
        $storage = new MemoryStorage();
        $name = new LockName('silly');
        $lock = new Lock($storage);
        $lock
            ->success(new CallbackHandler(function (Lock $lock) use($name) {
                $this::assertTrue($lock->isLocked($name));
                $lock->release($name);
                $this::assertFalse($lock->isLocked($name));
            }))
            ->fail(new FailCallbackHandler(function () {
                throw new AssertionFailedError('Success lock handler called');
            }))
            ->acquire($name)
        ;
    }

    public function testReleaseLock()
    {
        $storage = new MemoryStorage();
        $name = new LockName('silly');
        $lock = new Lock($storage);
        $lock
            ->success(new CallbackHandler(function (Lock $lock) use($name) {
                $this::assertTrue($lock->isLocked($name));
            }))
            ->fail(new FailCallbackHandler(function () {
                throw new AssertionFailedError('Success lock handler called');
            }))
            ->acquire($name)
        ;

        $lock->release($name);
        $this::assertFalse($lock->isLocked($name));
    }

    public function testFailedToAcquireAlreadyAcquiredLock()
    {
        $storage = new MemoryStorage();
        $name = new LockName('silly');
        $lock = new Lock($storage);
        $lock = $lock
            ->success(new CallbackHandler(function (Lock $lock) use($name) {
                $this::assertTrue($lock->isLocked($name));
            }))
            ->fail(new FailCallbackHandler(function () {
                $this::assertTrue(true);
            }))
        ;
        $lock->acquire($name);

        $lock->acquire($name);
    }

    public function testCanAcquireReleasedLock()
    {
        $storage = new MemoryStorage();
        $name = new LockName('silly');
        $lock = new Lock($storage);
        $lock = $lock
            ->success(new CallbackHandler(function (Lock $lock) use($name) {
                $this::assertTrue($lock->isLocked($name));
            }))
            ->fail(new FailCallbackHandler(function () {
                throw new AssertionFailedError('Success lock handler called');
            }))
        ;

        $lock->acquire($name);
        $lock->release($name);
        $lock->acquire($name);
        $this::assertTrue($lock->isLocked($name));
    }

    public function testLockWithTimeout()
    {
        $storage = new MemoryStorage();
        $name = new LockName('silly');
        $lock = new Lock($storage, LockTimeout::fromSeconds(1));
        $lock = $lock
            ->success(new CallbackHandler(function () use ($name, $storage) {
                $this::assertTrue(true);
            }))
            ->fail(new FailCallbackHandler(function () {
                throw new AssertionFailedError('Fail lock handler called');
            }))
        ;
        $lock->acquire($name);
        sleep(1);
        $lock->acquire($name);
    }

    public function testFailLockWithTimeout()
    {
        $storage = new MemoryStorage();
        $name = new LockName('silly');
        $lock = new Lock($storage, LockTimeout::fromSeconds(1));
        $lock = $lock
            ->success(new CallbackHandler(function () use ($name, $storage) {
            }))
            ->fail(new FailCallbackHandler(function () {
                $this::assertTrue(true);
            }))
        ;
        $lock->acquire($name);
        $lock->acquire($name);
    }

    public function testAcquireLockWithoutHandlers()
    {
        $this::expectException(\RuntimeException::class);
        $storage = new MemoryStorage();
        $name = new LockName('silly');
        $lock = new Lock($storage, LockTimeout::fromSeconds(1));
        $lock->acquire($name);
    }

    public function testAcquireLockWithoutSuccessHandler()
    {
        $this::expectException(\RuntimeException::class);
        $storage = new MemoryStorage();
        $name = new LockName('silly');
        $lock = new Lock($storage, LockTimeout::fromSeconds(1));
        $lock->success(new CallbackHandler(function () {}));
        $lock->acquire($name);
    }

    public function testAcquireLockWithoutFailHandler()
    {
        $this::expectException(\RuntimeException::class);
        $storage = new MemoryStorage();
        $name = new LockName('silly');
        $lock = new Lock($storage, LockTimeout::fromSeconds(1));
        $lock->fail(new FailCallbackHandler(function () {}));
        $lock->acquire($name);
    }
}
