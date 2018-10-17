<?php

declare(strict_types = 1);

namespace chabior\Lock\Handler;

use chabior\Lock\Lock;

interface HandlerInterface
{
    public function handle(Lock $lock): void;
}
