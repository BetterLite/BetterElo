<?php

namespace Mellooh\BetterElo\libs\Himari\query;

use Mellooh\BetterElo\libs\Himari\exception\QueryNotFoundException;

class QueryRepository {

    private array $queries = [];

    public function add(QueryDefinition $definition): void
    {
        $this->queries[$definition->name] = $definition->sql;
    }

    public function get(string $name): string{
        if (!isset($this->queries[$name])) {
            throw new QueryNotFoundException('Query not found: ' . $name);
        }

        return $this->queries[$name];
    }
}