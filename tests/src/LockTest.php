<?php

declare(strict_types = 1);

namespace chabior\Lock\Tests;

use chabior\Lock\Exception\LockException;
use chabior\Lock\Handler\CallbackHandler;
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
            ->success(new CallbackHandler(function () use ($name, $storage) {
                $this::assertTrue($storage->isLocked($name));
            }))
            ->fail(new CallbackHandler(function () {
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
            ->fail(new CallbackHandler(function () {
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
            ->success(new CallbackHandler(function (Lock $lock) use($name, $storage) {
                $this::assertTrue($storage->isLocked($name));
                $lock->release($name);
                $this::assertFalse($storage->isLocked($name));
            }))
            ->fail(new CallbackHandler(function () {
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
            ->success(new CallbackHandler(function () use($name, $storage) {
                $this::assertTrue($storage->isLocked($name));

            }))
            ->fail(new CallbackHandler(function () {
                throw new AssertionFailedError('Success lock handler called');
            }))
            ->acquire($name)
        ;

        $lock->release($name);
        $this::assertFalse($storage->isLocked($name));
    }

    public function testFailedToAcquireAlreadyAcquiredLock()
    {
        $storage = new MemoryStorage();
        $name = new LockName('silly');
        $lock = new Lock($storage);
        $lock = $lock
            ->success(new CallbackHandler(function () use($name, $storage) {
                $this::assertTrue($storage->isLocked($name));

            }))
            ->fail(new CallbackHandler(function () {
                throw new AssertionFailedError('Success lock handler called');
            }))
        ;
        $lock->acquire($name);

        $this::expectException(LockException::class);
        $lock->acquire($name);
    }

    public function testCanAcquireReleasedLock()
    {
        $storage = new MemoryStorage();
        $name = new LockName('silly');
        $lock = new Lock($storage);
        $lock = $lock
            ->success(new CallbackHandler(function () use($name, $storage) {
                $this::assertTrue($storage->isLocked($name));

            }))
            ->fail(new CallbackHandler(function () {
                throw new AssertionFailedError('Success lock handler called');
            }))
        ;

        $lock->acquire($name);
        $lock->release($name);
        $lock->acquire($name);
        $this::assertTrue($storage->isLocked($name));
    }
}
