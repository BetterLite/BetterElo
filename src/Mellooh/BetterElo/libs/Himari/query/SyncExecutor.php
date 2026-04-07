<?php

namespace Mellooh\BetterElo\libs\Himari\query;

use Mellooh\BetterElo\libs\Himari\async\Deferred;
use Mellooh\BetterElo\libs\Himari\async\Executor;
use Mellooh\BetterElo\libs\Himari\async\Promise;
use Throwable;

class SyncExecutor implements Executor{

    public function submit(callable $task, ?float $timeoutSeconds = null): Promise{
        $deferred = new Deferred();

        try {
            $result = $task();
            $deferred->resolve($result);
        } catch (Throwable $e) {
            $deferred->reject($e);
        }

        return $deferred;
    }

    public function waitAll(): void{}

    public function shutdown(): void{}
}