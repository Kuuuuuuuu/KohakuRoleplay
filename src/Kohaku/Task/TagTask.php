<?php

namespace Kohaku\Task;

use pocketmine\scheduler\Task;
use Kohaku\Core;
use pocketmine\Player;
use pocketmine\Server;

class TagTask extends Task {

	public function onRun(int $tick){
		foreach(Server::getInstance()->getOnlinePlayers() as $players){
		    $player = $players->getPlayer();
            $name = $players->getName();
            $ping = $players->getPing($name);
            $hp = round($players->getHealth(), 1);
            $max_hp = $players->getMaxHealth();
            $line = "\n";
            $color = "§a";
            if($ping < 100) $color = "§a";
            if($ping > 101) $color = "§6";
            $tag = "§f[§b{os} §f| §b{fakeos}§f]\n{color}{ping}ms §f| §c{hp} HP";
		    $tag = str_replace("&", "§", $tag);
		    $tag = str_replace("{color}", $color, $tag);
            $tag = str_replace("{name}", $name, $tag);
            $tag = str_replace("{hp}", $hp, $tag);
            $tag = str_replace("{ping}", $ping, $tag);
            $device = Core::$utils->getPlayerControls($player);
		    $os = Core::$utils->getPlayerOs($player);
		    $fakeos = Core::$utils->getFakeOs($player);
            $tag = str_replace("{device}", $device, $tag);
		    $tag = str_replace("{os}", $os, $tag);
		    $tag = str_replace("{fakeos}", $fakeos, $tag);
		    /**@var $jail **/
		    $jail = "§f[§b{os} §f| §b{fakeos}§f]\n{color}{ping}ms §f| §c{hp} HP\n§6Jail §eTime: §f {jail}";
		    $jail = str_replace("&", "§", $jail);
		    $jail = str_replace("{color}", $color, $jail);
            $jail = str_replace("{name}", $name, $jail);
            $jail = str_replace("{hp}", $hp, $jail);
            $jail = str_replace("{ping}", $ping, $jail);
            $jail = str_replace("{device}", $device, $jail);
		    $jail = str_replace("{os}", $os, $jail);
		    $jail = str_replace("{fakeos}", $fakeos, $jail);
		    $jail = str_replace("{jail}", Core::getInstance()->JailTime[$name] ?? 0, $jail);
		    if(isset(Core::getInstance()->Jail[$name])) {
		        if(Core::getInstance()->Jail[$name] === "no") {
		            $players->setScoreTag($tag);
		        }
		        if(Core::getInstance()->Jail[$name] === "yes") {
			        $players->setScoreTag($jail);
		        }
		    } else {
			  Core::getInstance()->Jail[$name] = "no";
			  print("Add isset jail to". $name);
			}
        }
    }
}