<?php

declare(strict_types=1);

namespace Mellooh\BetterElo\form;

use Mellooh\BetterElo\libs\FormX\CustomForm;
use pocketmine\player\Player;

class LeaderboardForm {

    public static function create(array $rows, string $title, string $emptyMsg, string $format, string $footer): CustomForm {
        $form = new CustomForm(function(Player $player, ?array $data) {
        });
        $form->setTitle($title);
        $form->addLabel(empty($rows) ? $emptyMsg : self::buildContent($rows, $format));
        $form->addLabel($footer);
        return $form;
    }

    private static function buildContent(array $rows, string $format): string {
        $content = [];
        foreach ($rows as $i => $row) {
            $position = $i + 1;
            $content[] = str_replace(
                ["{POSITION}", "{PLAYER}", "{ELO}"],
                [$position, $row["player_name"], $row["elo"]],
                $format
            );
        }
        return implode("\n", $content);
    }
}