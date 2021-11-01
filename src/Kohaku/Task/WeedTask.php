<?php

namespace Kohaku\Task;

use pocketmine\scheduler\Task;
use Kohaku\Core;
use pocketmine\Player;
use pocketmine\item\{Item, ItemIds};
use pocketmine\Server;

class WeedTask extends Task {

	public function onRun(int $tick){
		foreach(Server::getInstance()->getOnlinePlayers() as $players){
			$name = $players->getName();
		    if(isset(Core::getInstance()->getWeed[$name])){ 
               if(Core::getInstance()->getWeed[$name] === "yes"){
			       if(!isset(Core::getInstance()->getWeedProcess[$name])) {
			          Core::getInstance()->getWeedProcess[$name] = 0;
			       }
				   if(isset(Core::getInstance()->getWeedProcess[$name])) {
				      Core::getInstance()->getWeedProcess[$name]++;
				      $players->sendTip("§6Collect §aWeed: §f" . Core::getInstance()->getWeedProcess[$name] . "/" . Core::getInstance()->MaxWeedProcess);
			          if(Core::getInstance()->getWeedProcess[$name] === 64) {
				          $players->sendTip("§aYou got 64 Weeds");
				          $players->sendTitle("§aComplete");
				          $inventory = $players->getInventory();
		                  $inventory->addItem(Item::get(464, 0, 64));
		                  $inventory->sendContents($players);
		                  Core::getInstance()->getWeedProcess[$name] = 0;
				       }
                   }
               }
           }
       }
   }
}