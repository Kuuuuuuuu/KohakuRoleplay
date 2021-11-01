<?php

namespace Kohaku\Task;

use pocketmine\scheduler\Task;
use Kohaku\Core;
use pocketmine\Player;
use pocketmine\Server;

class MarketTask extends Task {

	public function onRun(int $tick){
		$apple = substr(str_shuffle("0123456789"), 0, 3);
		$weed = substr(str_shuffle("0123456789"), 0, 3);
		$wheat = substr(str_shuffle("0123456789"), 0, 3);
		$fist = substr(str_shuffle("0123456789"), 0, 3);
		Core::getInstance()->ApplePrice = $apple;
		Core::getInstance()->WeedPrice = $weed;
		Core::getInstance()->WheatPrice = $wheat;
		Core::getInstance()->FishPrice = $fist;
		Server::getInstance()->broadcastMessage(Core::getInstance()->getPrefixCore() . "§a§lร้านค้าได้อัพเดทราคาตลาดแล้ว");
    }
}
			