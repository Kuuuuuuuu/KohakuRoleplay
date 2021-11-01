<?php

namespace Kohaku\utils\Entities;

use pocketmine\entity\Entity;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Kohaku\Core;

class CoreManager {
	
	public function setKFCShop(Player $player) {
		$player->saveNBT();
		$nbt = Entity::createBaseNBT(new Vector3((float)$player->getX(), (float)$player->getY(), (float)$player->getZ()));
		$nbt->setTag(clone $player->namedtag->getCompoundTag("Skin"));
		$human = new KFCCustomer($player->getLevel(), $nbt);
		$human->setNameTagVisible(true);
		$human->setNameTagAlwaysVisible(true);
		$human->yaw = $player->getYaw();
		$human->pitch = $player->getPitch();
		$human->spawnToAll();
	}
	
	 public function setSeller(Player $player) {
		$player->saveNBT();
		$nbt = Entity::createBaseNBT(new Vector3((float)$player->getX(), (float)$player->getY(), (float)$player->getZ()));
		$nbt->setTag(clone $player->namedtag->getCompoundTag("Skin"));
		$human = new Seller($player->getLevel(), $nbt);
		$human->setNameTagVisible(true);
		$human->setNameTagAlwaysVisible(true);
		$human->yaw = $player->getYaw();
		$human->pitch = $player->getPitch();
		$human->spawnToAll();
	}
}