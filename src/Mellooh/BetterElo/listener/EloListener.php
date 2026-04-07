<?php

declare(strict_types=1);

namespace Mellooh\BetterElo\listener;

use Mellooh\BetterElo\database\DatabaseManager;
use Mellooh\BetterElo\Main;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;

class EloListener implements Listener {

    private Main $plugin;
    private DatabaseManager $database;

    public function __construct(Main $plugin, DatabaseManager $database) {
        $this->plugin = $plugin;
        $this->database = $database;
    }

    public function onDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        $cause = $player->getLastDamageCause();

        if ($cause instanceof EntityDamageByEntityEvent) {
            $killer = $cause->getDamager();
            if ($killer instanceof Player) {
                $this->addElo($killer, $this->plugin->getEloPerKill());
                $killer->sendMessage($this->plugin->translate("elo_gained", ["{AMOUNT}" => $this->plugin->getEloPerKill()]));
            }
        }

        $this->addElo($player, -$this->plugin->getEloPerDeath());
        $player->sendMessage($this->plugin->translate("elo_lost", ["{AMOUNT}" => $this->plugin->getEloPerDeath()]));
    }

    private function addElo(Player $player, int $amount): void {
        $name = $player->getName();
        $this->database->getPlayerElo($name, function(array $rows) use ($player, $amount) {
            if (empty($rows)) {
                $newElo = $amount < 0 ? 0 : $amount;
                $this->database->setPlayerElo($player->getName(), $newElo);
            } else {
                $newElo = (int)$rows[0]["elo"] + $amount;
                if ($newElo < 0) $newElo = 0;
                $this->database->setPlayerElo($player->getName(), $newElo);
            }
        });
    }
}