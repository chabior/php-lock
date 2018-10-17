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
        $name = new LockName('silly');
        $lock = new Lock(new MemoryStorage());
        $lock
            ->when(
                function () use($name) {
                    $this::assertTrue(true);
                },
                function () {
                    throw new AssertionFailedError('Failed handler called');
                }
            )
            ->acquire($name)
        ;
    }

    public function testFailLock()
    {
        $name = new LockName('silly');
        $lock = new Lock(new FailStorage());
        $lock
            ->when(
                function () {
                    throw new AssertionFailedError('Failed handler called');
                },
                function () {
                    $this::assertTrue(true);
                }
            )
            ->acquire($name)
        ;
    }
}
