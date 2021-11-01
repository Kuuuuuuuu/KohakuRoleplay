<?php

namespace Kohaku\Task;

use pocketmine\Server;
use pocketmine\scheduler\Task;
use Kohaku\utils\Entities\{KFCCustomer, Seller};
use Kohaku\Core;

class NPCTask extends Task {
	
	public function onRun(int $currentTick){
		$level = Server::getInstance()->getDefaultLevel();
		foreach ($level->getEntities() as $entity) {
			if ($entity instanceof KFCCustomer) {
				$entity->setNameTag("§cKFC§fCustomer\n§7Click to Buy");
				$entity->setNameTagAlwaysVisible(true);
				$entity->setImmobile(true);
				$entity->setScale(1);
			}
			if ($entity instanceof Seller) {
				$entity->setNameTag("§aSeller\n§7Click to Sell");
				$entity->setNameTagAlwaysVisible(true);
				$entity->setImmobile(true);
				$entity->setScale(1);
		    }
		}
	}
}