<?php

namespace Kohaku\Task;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use Kohaku\Core;
use pocketmine\math\Vector3;
use pocketmine\entity\{Effect, EffectInstance};
use pocketmine\Server;

class JailTask extends Task {
	
	public function onRun(int $currentTick) : void {
		foreach(Server::getInstance()->getOnlinePlayers() as $players){
		   $player = $players;
		   $name = $player->getName();
            if($player->isOnline() === true) {
            	if(!isset(Core::getInstance()->Jail[$name])) {
           	    Core::getInstance()->Jail[$name] = "no";
                }
                if(isset(Core::getInstance()->Jail[$name])) {
                    if(Core::getInstance()->Jail[$name] === "yes") {
                    	if(!isset(Core::getInstance()->JailTime[$name])) {
                   	    Core::getInstance()->JailTime[$name] = Core::getInstance()->MaxJailTime;
                         }
                         if(isset(Core::getInstance()->JailTime[$name])) {
                         	if(Core::getInstance()->JailTime[$name] === Core::getInstance()->MaxJailTime) {
                         	    $player->teleport(new Vector3(156, 4, 282));
                              }
                          	if(Core::getInstance()->JailTime[$name] !== 0) {
                          	    Core::getInstance()->JailTime[$name]--;
                              	$eff3 = new EffectInstance(Effect::getEffect(Effect::WEAKNESS) , 999999, 255, false);
	                              $player->addEffect($eff3);
		                          $player->sendTip("§6Jail §eTime: " . Core::getInstance()->JailTime[$player->getName()]);
			                  }
			                  if(Core::getInstance()->JailTime[$name] === 0) {
				                  $player->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
				                  $player->removeAllEffects();
				                  $player->sendMessage(Core::getInstance()->getPrefixCore() . "§aNow You leave Prison");
				                  unset(Core::getInstance()->JailTime[$name]);
				                  Core::getInstance()->Jail[$name] = "no";
				             }
			             }
		             }
		         }
             }
         }
     }
 }