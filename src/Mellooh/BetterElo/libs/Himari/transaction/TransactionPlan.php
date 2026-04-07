<?php

namespace Mellooh\BetterElo\libs\Himari\transaction;

use Mellooh\BetterElo\libs\Himari\transaction\TransactionStep;

class TransactionPlan {

    private array $steps = [];

    public function add(string $queryName, array $params = []): self{
        $this->steps[] = new TransactionStep($queryName, $params);
        return $this;
    }

    public function getSteps(): array{
        return $this->steps;
    }
}