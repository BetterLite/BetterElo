<?php

declare(strict_types=1);

namespace Mellooh\BetterElo\command;

use Mellooh\BetterElo\Main;
use Mellooh\BetterElo\database\DatabaseManager;
use Mellooh\BetterElo\form\LeaderboardForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TopEloCommand extends Command {

    private Main $plugin;
    private DatabaseManager $database;

    public function __construct(Main $plugin, DatabaseManager $database) {
        parent::__construct("topelo", "View top 10 elo leaderboard", "/topelo", ["leaderboard"]);
        $this->setPermission("betterelo.topelo");
        $this->plugin = $plugin;
        $this->database = $database;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game");
            return true;
        }

        $plugin = $this->plugin;
        $title = $plugin->translate("leaderboard_title");
        $emptyMsg = $plugin->translate("leaderboard_empty");
        $format = $plugin->translate("leaderboard_format");
        $footer = $plugin->translate("leaderboard_footer");

        $this->database->getTopElo(function(array $rows) use ($sender, $title, $emptyMsg, $format, $footer) {
            $form = LeaderboardForm::create($rows, $title, $emptyMsg, $format, $footer);
            $sender->sendForm($form);
        });

        return true;
    }
}