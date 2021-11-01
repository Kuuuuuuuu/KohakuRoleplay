<?php

namespace Kohaku\Execute;

use pocketmine\Player;
use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use Kohaku\Core;

class TcheckCommand  extends PluginCommand {
	
	private $plugin;
	
	public function __construct(Core $plugin){
		parent::__construct("tcheck", $plugin);
		$this->plugin=$plugin;
		$this->setDescription("UnBan Players");
	}
	
	public function execute(CommandSender $player, string $commandLabel, array $args){
		$name = $player->getName();
		if(!$player->isOp()) {
			$player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou cannot execute this command.");
        } if($player->isOp()) {
		  Core::$ban->openTcheckUI($player);
	    }
	}
}