<?php

declare(strict_types=1);

namespace Mellooh\BetterElo\database;

use Mellooh\BetterElo\libs\Himari\Himari;
use Mellooh\BetterElo\libs\Himari\config\DatabaseConfig;
use Mellooh\BetterElo\libs\Himari\core\Connector;
use pocketmine\plugin\Plugin;

class DatabaseManager {

    private Connector $connector;
    private Plugin $plugin;

    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
    }

    public function initialize(): void {
        $config = new DatabaseConfig(
            "sqlite",
            ["database" => $this->plugin->getDataFolder() . "elo.db"]
        );

        $resource = $this->plugin->getResource("sql/sqlite.sql");
        if ($resource === null) {
            throw new \RuntimeException("SQL resource not found");
        }

        $tempFile = tempnam(sys_get_temp_dir(), "himari_");
        $content = stream_get_contents($resource);
        file_put_contents($tempFile, $content);
        fclose($resource);

        $this->connector = Himari::createConnector(
            $this->plugin,
            $config,
            [$tempFile]
        );

        $this->plugin->getLogger()->info("Creating table with Himari...");
        $this->connector->execute("create_table", [], function() {
            $this->plugin->getLogger()->info("Table created successfully");
        }, function($e) {
            $this->plugin->getLogger()->error("Failed to create table: " . $e->getMessage());
        });
    }

    public function getConnector(): Connector {
        return $this->connector;
    }

    public function getPlayerElo(string $playerName, callable $onSuccess, ?callable $onError = null): void {
        $this->connector->select("get_elo", ["player_name" => $playerName], $onSuccess, $onError);
    }

    public function setPlayerElo(string $playerName, int $elo, ?callable $onSuccess = null, ?callable $onError = null): void {
        $this->connector->select("get_elo", ["player_name" => $playerName], function(array $rows) use ($playerName, $elo, $onSuccess, $onError) {
            if (empty($rows)) {
                $this->connector->insert("insert_elo", [
                    "player_name" => $playerName,
                    "elo" => $elo
                ], $onSuccess ?? function() {}, $onError ?? function($e) { $this->plugin->getLogger()->error($e->getMessage()); });
            } else {
                $this->connector->change("update_elo", [
                    "elo" => $elo,
                    "player_name" => $playerName
                ], $onSuccess ?? function() {}, $onError ?? function($e) { $this->plugin->getLogger()->error($e->getMessage()); });
            }
        }, $onError);
    }

    public function getTopElo(callable $onSuccess, ?callable $onError = null): void {
        $this->connector->select("get_top", [], $onSuccess, $onError);
    }

    public function shutdown(): void {
        $this->connector->waitAll();
        $this->connector->shutdown();
    }
}