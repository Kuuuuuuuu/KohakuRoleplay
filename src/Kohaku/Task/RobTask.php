<?php

namespace Kohaku\Task;

use pocketmine\scheduler\Task;
use Kohaku\Core;
use pocketmine\Player;
use pocketmine\Server;

class RobTask extends Task {

	public function onRun(int $tick){
		foreach(Server::getInstance()->getOnlinePlayers() as $players){
			$name = $players->getName();
		    if(isset(Core::getInstance()->Rob[$name])){ 
               if(Core::getInstance()->Rob[$name] === "yes"){
			       if(!isset(Core::getInstance()->RobProcess[$name])) {
			          Core::getInstance()->RobProcess[$name] = 0;
			       }
				   if(isset(Core::getInstance()->RobProcess[$name])) {
				      Core::getInstance()->RobProcess[$name]++;
				      $players->sendTip("§bProgress: §f" . Core::getInstance()->RobProcess[$name] . "/" . Core::getInstance()->MaxRobProgress);
			          if(Core::getInstance()->RobProcess[$name] === Core::getInstance()->MaxRobProgress) {
				          if(!isset(Core::getInstance()->DirtyMoney[$name])) {
				             Core::getInstance()->DirtyMoney[$name] = 0;
				          }
					      if(isset(Core::getInstance()->DirtyMoney[$name])) {
					         $money = substr(str_shuffle("0123456789"), 0, 4);
                             $add = Core::getInstance()->DirtyMoney[$name] + $money;
    	                     Core::getInstance()->DirtyMoney[$name] = $add;
                             $players->sendTip("§aYou Robbed And Got: §f" . Core::getInstance()->DirtyMoney[$name]);
                             $players->sendTitle("§aComplete");
                             unset(Core::getInstance()->RobProcess[$name]);
                             unset(Core::getInstance()->Rob[$name]);
                             Server::getInstance()->broadcastMessage(Core::getInstance()->getPrefixCore() . "§c§lมีคนปล้นธนาคาร");
                          }
                      }
                   }
               }
           }
       }
   }
}