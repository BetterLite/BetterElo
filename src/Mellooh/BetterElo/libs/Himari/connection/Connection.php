<?php

namespace Mellooh\BetterElo\libs\Himari\connection;

use Mellooh\BetterElo\libs\Himari\connection\Result;

interface Connection{

    public function execute(string $sql, array $params = []): Result;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollBack(): void;
}