<?php

namespace Kohaku\Task;

use pocketmine\scheduler\Task;
use Kohaku\Core;
use pocketmine\Player;
use pocketmine\item\{Item, ItemIds};
use pocketmine\Server;

class AppleTask extends Task {

	public function onRun(int $tick){
		foreach(Server::getInstance()->getOnlinePlayers() as $players){
			$name = $players->getName();
		    if(isset(Core::getInstance()->getApple[$name])){ 
               if(Core::getInstance()->getApple[$name] === "yes"){
			       if(!isset(Core::getInstance()->getAppleProcess[$name])) {
			          Core::getInstance()->getAppleProcess[$name] = 0;
			       }
				   if(isset(Core::getInstance()->getAppleProcess[$name])) {
				      Core::getInstance()->getAppleProcess[$name]++;
				      $players->sendTip("§6Collect §cApple: §f" . Core::getInstance()->getAppleProcess[$name] . "/" . Core::getInstance()->MaxAppleProcess);
			          if(Core::getInstance()->getAppleProcess[$name] === 64) {
				          $players->sendTip("§aYou got 64 Apples");
				          $players->sendTitle("§aComplete");
				          $inventory = $players->getInventory();
		                  $inventory->addItem(Item::get(260, 0, 64));
		                  $inventory->sendContents($players);
		                  Core::getInstance()->getAppleProcess[$name] = 0;
				       }
                   }
               }
           }
       }
   }
}