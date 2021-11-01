<?php

namespace Kohaku\Task;

use pocketmine\entity\object\ItemEntity;
use pocketmine\entity\Human;
use pocketmine\entity\projectile\Arrow;
use pocketmine\entity\{Effect, EffectInstance};
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\math\Vector3;
use Kohaku\Core;

class RespawnTask extends Task {

	public function onRun(int $currentTick) : void{
		foreach(Server::getInstance()->getOnlinePlayers() as $players){
		    if($players->isOnline() === false) return;
		    if(isset(Core::getInstance()->Respawn[$players->getName()])) {
			    if(Core::getInstance()->Respawn[$players->getName()] > 0) {
				    Core::getInstance()->Respawn[$players->getName()]--;
				    $eff3 = new EffectInstance(Effect::getEffect(Effect::WEAKNESS) , 999999, 255, false);
					$eff2 = new EffectInstance(Effect::getEffect(Effect::BLINDNESS) , 999999, 255, false);
					$eff = new EffectInstance(Effect::getEffect(Effect::INVISIBILITY) , 999999, 255, false);
				    $players->addEffect($eff);
				    $players->addEffect($eff2);
			        $players->addEffect($eff3);
			        $players->teleport(new Vector3(263, 57, 218));
				    $players->sendTip("§aRespawn §fTime: " . Core::getInstance()->Respawn[$players->getName()]);
				}
			   if(Core::getInstance()->Respawn[$players->getName()] === 0) {
				   $players->removeAllEffects();
				   $players->sendMessage(Core::getInstance()->getPrefixCore() . "§aYour are Repsawned!");
				   $players->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
				   unset(Core::getInstance()->Respawn[$players->getName()]);
			 }
	      }
	   }
	}
}