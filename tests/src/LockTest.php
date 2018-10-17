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
        $lock = new Lock(new MemoryStorage());
        $lock
            ->acquire(new LockName('silly'))
            ->then(
                function () use($lock) {
                    $this::assertTrue(true);
                },
                function () {
                    throw new AssertionFailedError('Fail handler called!');
                }
            )
            ->resolve()
        ;
    }

    public function testFailLock()
    {
        $lock = new Lock(new FailStorage());
        $lock
            ->acquire(new LockName('silly'))
            ->then(
                function () {
                    throw new AssertionFailedError('Fail handler called!');
                },
                function () {
                    $this::assertTrue(true);
                }
            )
            ->resolve()
        ;
    }
}
