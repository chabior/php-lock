<?php

declare(strict_types = 1);

namespace chabior\Lock\Handler;

use chabior\Lock\Lock;

class CallbackHandler implements HandlerInterface
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function handle(Lock $lock): void
    {
        $this->callback->__invoke($lock);
    }
}
