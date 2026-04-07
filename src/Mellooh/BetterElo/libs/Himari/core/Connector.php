<?php

namespace Mellooh\BetterElo\libs\Himari\core;

use Mellooh\BetterElo\libs\Himari\async\Executor;
use Mellooh\BetterElo\libs\Himari\async\task\SqlTask;
use Mellooh\BetterElo\libs\Himari\connection\Result;
use Mellooh\BetterElo\libs\Himari\log\Logger;
use Mellooh\BetterElo\libs\Himari\query\QueryRepository;
use Throwable;

class Connector {

    private QueryRepository $queries;
    private Executor $executor;
    private ?Logger $logger;

    public function __construct(QueryRepository $queries, Executor $executor, ?Logger $logger = null){
        $this->queries = $queries;
        $this->executor = $executor;
        $this->logger = $logger;
    }

    public function select(string $name, array $params, callable $onSuccess, ?callable $onError = null, ?float $timeoutSeconds = null): void{
        $sql = $this->queries->get($name);
        $task = new SqlTask($sql, $params);

        $promise = $this->executor->submit($task, $timeoutSeconds);

        $promise->then(
            function(Result $result) use ($onSuccess): void{
                $onSuccess($result->rows);
            },
            function(Throwable $e) use ($onError): void{
                if($onError !== null){
                    $onError($e);
                }
            }
        );
    }

    public function execute(string $name, array $params, callable $onSuccess, ?callable $onError = null, ?float $timeoutSeconds = null): void{
        $sql = $this->queries->get($name);
        $task = new SqlTask($sql, $params);

        $promise = $this->executor->submit($task, $timeoutSeconds);

        $promise->then(
            function(Result $result) use ($onSuccess): void{
                $onSuccess($result);
            },
            function(Throwable $e) use ($onError): void{
                if($onError !== null){
                    $onError($e);
                }
            }
        );
    }

    public function insert(string $name, array $params, callable $onSuccess, ?callable $onError = null, ?float $timeoutSeconds = null): void{
        $this->execute(
            $name,
            $params,
            function(Result $result) use ($onSuccess): void{
                $onSuccess($result->lastInsertId);
            },
            $onError,
            $timeoutSeconds
        );
    }

    public function change(string $name, array $params, callable $onSuccess, ?callable $onError = null, ?float $timeoutSeconds = null): void{
        $this->execute(
            $name,
            $params,
            function(Result $result) use ($onSuccess): void{
                $onSuccess($result->affectedRows);
            },
            $onError,
            $timeoutSeconds
        );
    }

    public function waitAll(): void{
        $this->executor->waitAll();
    }

    public function shutdown(): void{
        $this->executor->shutdown();
    }
}