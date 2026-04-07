<?php

namespace Mellooh\BetterElo\libs\Himari\async;

use Mellooh\BetterElo\libs\Himari\async\task\QueryTask;
use Mellooh\BetterElo\libs\Himari\async\task\SqlTask;
use Mellooh\BetterElo\libs\Himari\config\DatabaseConfig;
use Mellooh\BetterElo\libs\Himari\connection\Result;
use pocketmine\plugin\PluginBase;
use RuntimeException;

class AsyncExecutor implements Executor{

    private static array $instances = [];

    private PluginBase $plugin;
    private array $dbConfig;
    private array $pending = [];
    private int $nextId = 1;
    private bool $isShutdown = false;

    public function __construct(PluginBase $plugin, DatabaseConfig $config){
        $this->plugin = $plugin;
        $this->dbConfig = [
            'driver' => $config->driver,
            'parameters' => $config->parameters,
            'username' => $config->username,
            'password' => $config->password,
            'options' => $config->options,
        ];

        self::$instances[$plugin->getName()] = $this;
    }

    public static function getInstance(string $pluginName): ?self{
        return self::$instances[$pluginName] ?? null;
    }

    public function submit(callable $task, ?float $timeoutSeconds = null): Promise{
        if($this->isShutdown){
            $deferred = new Deferred();
            $deferred->reject(new RuntimeException('Executor is shut down'));
            return $deferred;
        }

        if(!$task instanceof SqlTask){
            $deferred = new Deferred();
            $deferred->reject(new RuntimeException('Unsupported task type for PocketMineAsyncExecutor'));
            return $deferred;
        }

        $sql = $task->sql;
        $params = $task->params;

        $deferred = new Deferred();
        $id = $this->nextId++;
        $this->pending[$id] = $deferred;

        $queryTask = new QueryTask(
            $this->plugin->getName(),
            $id,
            $this->dbConfig,
            $sql,
            $params,
            $timeoutSeconds
        );

        $this->plugin->getServer()->getAsyncPool()->submitTask($queryTask);

        return $deferred;
    }

    public function complete(int $id, ?array $resultData, ?array $errorData): void{
        if(!isset($this->pending[$id])){
            return;
        }

        $deferred = $this->pending[$id];
        unset($this->pending[$id]);

        if($errorData !== null){
            $e = new RuntimeException($errorData['message'], $errorData['code']);
            $deferred->reject($e);
            return;
        }

        $rows = $resultData['rows'] ?? [];
        $affected = (int)($resultData['affected'] ?? 0);
        $lastInsertId = $resultData['lastInsertId'] ?? null;

        $result = new Result(
            $rows,
            $affected,
            $lastInsertId !== null ? (int)$lastInsertId : null
        );

        $deferred->resolve($result);
    }

    public function waitAll(): void{
    }

    public function shutdown(): void{
        $this->isShutdown = true;
        $this->pending = [];
        unset(self::$instances[$this->plugin->getName()]);
    }
}