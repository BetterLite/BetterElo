<?php

declare(strict_types=1);

namespace Mellooh\BetterElo;

use Mellooh\BetterElo\command\MyEloCommand;
use Mellooh\BetterElo\command\TopEloCommand;
use Mellooh\BetterElo\database\DatabaseManager;
use Mellooh\BetterElo\listener\EloListener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase {

    private DatabaseManager $database;

    public function onLoad(): void {
        $this->saveDefaultConfig();
    }

    protected function onEnable(): void {
        $this->database = new DatabaseManager($this);
        $this->database->initialize();

        $this->getServer()->getCommandMap()->register("betterelo", new MyEloCommand($this, $this->database));
        $this->getServer()->getCommandMap()->register("betterelo", new TopEloCommand($this, $this->database));

        $this->getServer()->getPluginManager()->registerEvents(new EloListener($this, $this->database), $this);
    }

    protected function onDisable(): void {
        $this->database->shutdown();
    }

    public function getDatabase(): DatabaseManager {
        return $this->database;
    }

    public function getEloPerKill(): int {
        return $this->getConfig()->get("settings")["elo_per_kill"] ?? 10;
    }

    public function getEloPerDeath(): int {
        return $this->getConfig()->get("settings")["elo_per_death"] ?? 10;
    }

    public function translate(string $key, array $replace = []): string {
        $msg = $this->getConfig()->get("messages")[$key] ?? $key;
        foreach ($replace as $k => $v) {
            $msg = str_replace($k, (string)$v, $msg);
        }
        return TextFormat::colorize($msg);
    }
}