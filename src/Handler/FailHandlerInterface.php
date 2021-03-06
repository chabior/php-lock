<?php

declare(strict_types = 1);

namespace chabior\Lock\Handler;

use chabior\Lock\Lock;

interface FailHandlerInterface
{
    public function handle(Lock $lock, \Throwable $exception): void;
}
