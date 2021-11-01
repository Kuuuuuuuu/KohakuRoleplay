<?php

namespace Kohaku\Task;

use pocketmine\scheduler\Task;
use Kohaku\Core;
use pocketmine\Player;
use pocketmine\Server;

class WashMoneyTask extends Task {

	public function onRun(int $tick){
		foreach(Server::getInstance()->getOnlinePlayers() as $players){
			$name = $players->getName();
		    if(isset(Core::getInstance()->WashingMoney[$name])) {
			   if(Core::getInstance()->WashingMoney[$name] === "yes") {
				  if(!isset(Core::getInstance()->WashingProcess[$name])) {
				     Core::getInstance()->WashingProcess[$name] = 0;
				  }
				  if(!isset(Core::getInstance()->DirtyMoney[$name])) {
				     Core::getInstance()->DirtyMoney[$name] = 0;
				   }
			      if(isset(Core::getInstance()->WashingProcess[$name])) {
			         Core::getInstance()->WashingProcess[$name]++;
				     $players->sendTip("§bWashing §aMoney: §f". Core::getInstance()->WashingProcess[$name]. "/". Core::getInstance()->MaxWashingProcess);
			       }
				   if(isset(Core::getInstance()->DirtyMoney[$name])) {
					   if(Core::getInstance()->DirtyMoney[$name] >= 0) {
				           if(Core::getInstance()->WashingProcess[$name] === Core::getInstance()->MaxWashingProcess) {
				               Core::getInstance()->CleanMoney[$name] = Core::getInstance()->DirtyMoney[$name];
					           $players->sendTip("§aYou got Clean Money: §f" . Core::getInstance()->CleanMoney[$name]);
					           $players->sendTitle("§aComplete");
					           Core::getInstance()->eco->addMoney($players, Core::getInstance()->CleanMoney[$name]);
					           Core::getInstance()->CleanMoney[$name] = 0;
					           Core::getInstance()->WashingMoney[$name] = 0;
					           Core::getInstance()->WashingProcess[$name] = 0;
					           Core::getInstance()->DirtyMoney[$name] = 0;
				            }
		                 } else {
                           $player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou need more Dirty Money to Washing");
                           return;
                       }
                   }
               }
           }
       }
   }
}