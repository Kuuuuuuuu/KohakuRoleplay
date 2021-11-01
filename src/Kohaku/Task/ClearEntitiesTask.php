<?php

namespace Kohaku\Task;

use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\Human;
use pocketmine\entity\projectile\Arrow;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Kohaku\Core;

class ClearEntitiesTask extends Task {
	
	private int $cleared = 0;

	public function onRun(int $currentTick) : void{
		if(count(Server::getInstance()->getOnlinePlayers()) < 1) return;
		if(Core::getInstance()->ClearLagg < 1001) {
		   Core::getInstance()->ClearLagg++;
		}
		if(Core::getInstance()->ClearLagg > 1001) {
		   Core::getInstance()->ClearLagg = 0;
		}
		if(Core::getInstance()->ClearLagg === 970) {
			Server::getInstance()->broadcastMessage(Core::getInstance()->getPrefixCore() . "§cEntities/drops will be purged in 30 Seconds");
		}
		if(Core::getInstance()->ClearLagg === 990) {
		    Server::getInstance()->broadcastMessage(Core::getInstance()->getPrefixCore() . "§cEntities/drops will be purged in 10 Seconds");
		}
		if(Core::getInstance()->ClearLagg === 997) {
			Server::getInstance()->broadcastMessage(Core::getInstance()->getPrefixCore() . "§cEntities/drops will be purged in 3 Seconds");
		}
		if(Core::getInstance()->ClearLagg === 998) {
			Server::getInstance()->broadcastMessage(Core::getInstance()->getPrefixCore() . "§cEntities/drops will be purged in 2 Seconds");
		}
		if(Core::getInstance()->ClearLagg === 999) {
			Server::getInstance()->broadcastMessage(Core::getInstance()->getPrefixCore() . "§cEntities/drops will be purged in 1 Second");
		}
		if(Core::getInstance()->ClearLagg === 1000) {
		    foreach (Server::getInstance()->getLevels() as $level) {
			   foreach ($level->getEntities() as $entity) {
				   if($entity instanceof ItemEntity or $entity instanceof Arrow){
					  $entity->flagForDespawn();
					  $this->cleared++;
				   }
				}
			}
		}
		if(Core::getInstance()->ClearLagg === 1001) {
			Core::getInstance()->ClearLag = 0;
			Server::getInstance()->broadcastMessage(Core::getInstance()->getPrefixCore() . "§aCleared Entities/drops §f" . $this->cleared . " §aItems");
			$this->cleared = 0;

	    }
	}
}