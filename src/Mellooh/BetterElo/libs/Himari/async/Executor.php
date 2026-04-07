<?php

namespace Mellooh\BetterElo\libs\Himari\async;

use Mellooh\BetterElo\libs\Himari\async\Promise;

interface Executor {

    public function submit(callable $task, ?float $timeoutSeconds = null): Promise;

    public function waitAll(): void;

    public function shutdown(): void;
}