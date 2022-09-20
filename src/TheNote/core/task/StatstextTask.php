<?php

//   ╔═════╗╔═╗ ╔═╗╔═════╗╔═╗    ╔═╗╔═════╗╔═════╗╔═════╗
//   ╚═╗ ╔═╝║ ║ ║ ║║ ╔═══╝║ ╚═╗  ║ ║║ ╔═╗ ║╚═╗ ╔═╝║ ╔═══╝
//     ║ ║  ║ ╚═╝ ║║ ╚══╗ ║   ╚══╣ ║║ ║ ║ ║  ║ ║  ║ ╚══╗
//     ║ ║  ║ ╔═╗ ║║ ╔══╝ ║ ╠══╗   ║║ ║ ║ ║  ║ ║  ║ ╔══╝
//     ║ ║  ║ ║ ║ ║║ ╚═══╗║ ║  ╚═╗ ║║ ╚═╝ ║  ║ ║  ║ ╚═══╗
//     ╚═╝  ╚═╝ ╚═╝╚═════╝╚═╝    ╚═╝╚═════╝  ╚═╝  ╚═════╝
//   Copyright by TheNote! Not for Resale! Not for others
//

namespace TheNote\core\task;

use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\particle\FloatingTextParticle;
use TheNote\core\Main;

class StatstextTask extends Task
{

    private $plugin;
    private $floattext;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onRun() : void
    {

        $all = $this->plugin->getServer()->getOnlinePlayers();

        foreach ($all as $player) {
            if(!$player->isOnline()) return;

            $config = new Config($this->plugin->getDataFolder() . Main::$setup . "Config" . ".yml", Config::YAML);
			$level = $this->plugin->getServer()->getWorldManager()->getWorldByName($config->get("level"));

			$text = $this->getText($player);
            $x = $config->get("X");
            $y = $config->get("Y");
            $z = $config->get("Z");

            if ($this->plugin->anni === 1) {
                $this->plugin->anni = 2;
            } elseif ($this->plugin->anni === 2) {
                $this->plugin->anni = 1;
            }
            if ($config->get("leaderboard") == true) {
                if (!isset($this->floattext[$player->getName()])) {
                    # existiert noch nicht
                    $this->floattext[$player->getName()] = new FloatingTextParticle($text);
                    $particle = $this->floattext[$player->getName()];
                    #$packet = $particle->encode()
                    $particle->setInvisible(true);
                    $level->addParticle(new Vector3($x, $y, $z),$particle, [$player]);
                } else {
                    # is schon da
                    $particle = $this->floattext[$player->getName()];
                    $particle->setInvisible(true);
                    $level->addParticle(new Vector3($x, $y, $z), $particle, $all);
                    $this->floattext[$player->getName()] = new FloatingTextParticle($text);
                    $newparticle = $this->floattext[$player->getName()];
                    $newparticle->setInvisible(false);
                    $level->addParticle(new Vector3($x, $y, $z), $newparticle, [$player]);
                }
            }
        }
    }

    public function getText(Player $player)
    {
        $stats = new Config($this->plugin->getDataFolder() . Main::$statsfile . $player->getName() . ".json", Config::JSON);
        if ($this->plugin->anni === 1) {
            $text = "§6====§f[§eStatistiken§f]§6====\n" .
                "§eDeine Joins §f: §e" . $stats->get("joins") . "\n" .
                "§eDeine Sprünge §f: §e" . $stats->get("jumps") . "\n" .
                "§eDeine Kicks §f: §e" . $stats->get("kicks") . "\n" .
                "§eDeine Interacts §f: §e" . $stats->get("interact") . "\n" .
                "§eGelaufene Meter §f: §e" . round($stats->get("movewalk")) . "m\n" .
                "§eGeflogene Meter §f: §e" . round($stats->get("movefly")) . "m\n" .
                "§eBlöcke abgebaut §f: §e" . $stats->get("break") . "\n" .
                "§eBlöcke gesetzt §f: §e" . $stats->get("place") . "\n" .
                "§eGedroppte Items §f: §e" . $stats->get("drop") . "\n" .
                "§eGesammelte Items §f: §e" . $stats->get("pick") . "\n" .
                "§eKonsumierte Items §f: §e" . $stats->get("consume") . "\n" .
                "§eDeine Nachrrichten §f: §e" . $stats->get("messages") . "\n".
                "§eDeine Votes §f: §e" . $stats->get("votes");
        } else {
            $text = "§6====§f[§eStatistiken§f]§6====\n" .
                "§eDeine Joins §f: §e" . $stats->get("joins") . "\n" .
                "§eDeine Sprünge §f: §e" . $stats->get("jumps") . "\n" .
                "§eDeine Kicks §f: §e" . $stats->get("kicks") . "\n" .
                "§eDeine Interacts §f: §e" . $stats->get("interact") . "\n" .
                "§eGelaufene Meter §f: §e" . round($stats->get("movewalk")) . "m\n" .
                "§eGeflogene Meter §f: §e" . round($stats->get("movefly")) . "m\n" .
                "§eBlöcke abgebaut §f: §e" . $stats->get("break") . "\n" .
                "§eBlöcke gesetzt §f: §e" . $stats->get("place") . "\n" .
                "§eGedroppte Items §f: §e" . $stats->get("drop") . "\n" .
                "§eGesammelte Items §f: §e" . $stats->get("pick") . "\n" .
                "§eKonsumierte Items §f: §e" . $stats->get("consume") . "\n" .
                "§eDeine Nachrrichten §f: §e" . $stats->get("messages") . "\n".
                "§eDeine Votes §f: §e" . $stats->get("votes");
        }
        return $text;
    }
}