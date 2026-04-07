<?php

declare(strict_types=1);

namespace Mellooh\BetterElo\command;

use Mellooh\BetterElo\Main;
use Mellooh\BetterElo\database\DatabaseManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class MyEloCommand extends Command {

    private Main $plugin;
    private DatabaseManager $database;

    public function __construct(Main $plugin, DatabaseManager $database) {
        parent::__construct("myelo", "View your elo", "/myelo", ["elo"]);
        $this->setPermission("betterelo.myelo");
        $this->plugin = $plugin;
        $this->database = $database;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game");
            return true;
        }

        $plugin = $this->plugin;
        $this->database->getPlayerElo($sender->getName(), function(array $rows) use ($sender, $plugin) {
            if (empty($rows)) {
                $sender->sendMessage($plugin->translate("no_elo"));
                return;
            }
            $elo = $rows[0]["elo"];
            $sender->sendMessage($plugin->translate("myelo", ["{ELO}" => $elo]));
        });

        return true;
    }
}