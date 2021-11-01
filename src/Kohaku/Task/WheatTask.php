<?php

namespace Kohaku\Task;

use pocketmine\scheduler\Task;
use Kohaku\Core;
use pocketmine\Player;
use pocketmine\item\{Item, ItemIds};
use pocketmine\Server;

class WheatTask extends Task {

	public function onRun(int $tick){
		foreach(Server::getInstance()->getOnlinePlayers() as $players){
			$name = $players->getName();
		    if(isset(Core::getInstance()->getWheat[$name])){ 
               if(Core::getInstance()->getWheat[$name] === "yes"){
			       if(!isset(Core::getInstance()->getWheatProcess[$name])) {
			          Core::getInstance()->getWheatProcess[$name] = 0;
			       }
				   if(isset(Core::getInstance()->getWheatProcess[$name])) {
				      Core::getInstance()->getWheatProcess[$name]++;
				      $players->sendTip("§6Collect §eWheat: §f" . Core::getInstance()->getWheatProcess[$name] . "/" . Core::getInstance()->MaxWheatProcess);
			          if(Core::getInstance()->getWheatProcess[$name] === 64) {
				          $players->sendTip("§aYou got 64 Wheats");
				          $players->sendTitle("§aComplete");
				          $inventory = $players->getInventory();
		                  $inventory->addItem(Item::get(296, 0, 64));
		                  $inventory->sendContents($players);
		                  Core::getInstance()->getWheatProcess[$name] = 0;
				       }
                   }
               }
           }
       }
   }
}