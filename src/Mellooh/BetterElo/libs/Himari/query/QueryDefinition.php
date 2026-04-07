<?php

namespace Mellooh\BetterElo\libs\Himari\query;

class QueryDefinition {

    public function __construct(public readonly string $name, public readonly string $sql) {}
}