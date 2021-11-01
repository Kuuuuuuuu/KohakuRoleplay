<?php

namespace Kohaku\Task;

use pocketmine\scheduler\Task;
use Kohaku\Core;
use pocketmine\Player;
use pocketmine\item\{Item, ItemIds};
use pocketmine\Server;

class FishTask extends Task {

	public function onRun(int $tick){
		foreach(Server::getInstance()->getOnlinePlayers() as $players){
			$name = $players->getName();
		    if(isset(Core::getInstance()->getFish[$name])){ 
               if(Core::getInstance()->getFish[$name] === "yes"){
			       if(!isset(Core::getInstance()->getFishProcess[$name])) {
			          Core::getInstance()->getFishProcess[$name] = 0;
			       }
				   if(isset(Core::getInstance()->getFishProcess[$name])) {
				      Core::getInstance()->getFishProcess[$name]++;
				      $players->sendTip("§6Collect §9Fish: §f" . Core::getInstance()->getFishProcess[$name] . "/" . Core::getInstance()->MaxFishProcess);
			          if(Core::getInstance()->getFishProcess[$name] === 64) {
				          $players->sendTip("§aYou got 64 Fishs");
				          $players->sendTitle("§aComplete");
				          $inventory = $players->getInventory();
		                  $inventory->addItem(Item::get(349, 0, 64));
		                  $inventory->sendContents($players);
		                  Core::getInstance()->getFishProcess[$name] = 0;
				       }
                   }
               }
           }
       }
   }
}