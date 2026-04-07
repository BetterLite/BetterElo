<?php

namespace Mellooh\BetterElo\libs\Himari\transaction;

class TransactionStep {

    public function __construct(public readonly string $queryName, public readonly array $params = []) {}
}