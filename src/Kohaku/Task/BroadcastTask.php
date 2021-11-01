<?php

namespace Kohaku\Task;

use pocketmine\scheduler\Task;
use Kohaku\Core;
use function sprintf;
use pocketmine\utils\Process;

class BroadcastTask extends Task{
	
	public function __construct(Core $plugin){
		$this->plugin = $plugin;
		$this->line = -1;
	}
	
	public function onRun(int $tick):void{
		$d = Process::getRealMemoryUsage();
		$u = Process::getAdvancedMemoryUsage();
		$usage = sprintf("%g/%g/%g/%g MB @ %d threads", round(($u[0] / 1024) / 1024, 2), round(($d[0] / 1024) / 1024, 2), round(($u[1] / 1024) / 1024, 2), round(($u[2] / 1024) / 1024, 2), Process::getThreadCount());
		$cast = [ $this->plugin->getPrefixCore() . $usage, $this->plugin->getPrefixCore(). "§eเข้า Discord ได้ที่: §bhttps://discord.gg/XpTut4RSTS" ];
		$this->line++;
		$msg = $cast[$this->line];
		foreach($this->plugin->getServer()->getOnlinePlayers() as $online){
			$online->sendMessage($msg);
		}
		if($this->line === count($cast) - 1) $this->line = -1;
	}
}