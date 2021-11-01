<?php

namespace Kohaku\Task;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use Kohaku\utils\Scoreboards;
use Kohaku\Core;
use Kohaku\utils\math\Time;
use pocketmine\utils\Process;
use Kohaku\Arena\{Sumo, SchedulerSumo};
use pocketmine\Server;

class ScoreboardTask extends Task {
	
	private $titleIndex;
	private $player;
	private array $titles = ["§aR", "§aRo", "§aRol", "§aRole", "§aRoleP", "§aRolePl", "§aRolePla", "§aRolePlay", "§aRolePlay §f[", "§aRolePlay §f[§eB", "§aRolePlay §f[§eBe", "§aRolePlay §f[§eBet", "§aRolePlay §f[§eBeta", "§aRolePlay §f[§eBeta§f]", "§k§f&&&&&&&&&&"];
	
	
	public function __construct(Core $plugin, $player){
		$this->player = $player;
	}

	public function onRun(int $currentTick) : void {
		$player = $this->player;
		if($player->isOnline() === true) {
	       $this->sb($player);
        } else {
       	Core::getInstance()->getScheduler()->cancelTask($this->getTaskId());
        }
	}
	

	public function sb(Player $player) : void {
		$this->titleIndex++;
		$ping = $player->getPing() . "ms";
		$tpsColor = "§a";
        $server = Server::getInstance();
        if ($server->getTicksPerSecond() < 17) {
            $tpsColor = "§e";
        } 
        if ($server->getTicksPerSecond() < 12) {
            $tpsColor = "§c";
        }
        $rank = Core::getInstance()->getPurePerms()->getUserDataMgr()->getGroup($player);
		$money = Core::getInstance()->eco->myMoney($player);
		$on = count(Server::getInstance()->getOnlinePlayers());
		$lag = Core::getInstance()->ClearLagg;
		$dirty = Core::getInstance()->DirtyMoney[$player->getName()] ?? null;
		if($dirty === null) $dirty = 0;
		$lines = [
		    1 => "§7---------------§0",
		    2 => "§bOnline: §6$on/2021",
		    3 => "§bPing: §6$ping",
		    4 => "§bTPS: {$tpsColor}{$server->getTicksPerSecond()}",
		    5 => "§d",
		    6 => "§fYour §bRank: §6$rank",
		    7 => "§fYour §bMoney: §6$money",
		    8 => "§cDirty §eMoney: §6$dirty",
		    9 => "§6",
		    10 => "§bClearLagg: §6$lag",
		    11 => "§7---------------"
		];
		
		if(!isset($this->titles[$this->titleIndex])) $this->titleIndex = 0;
		Core::$score->new($player, "ObjectiveName", $this->titles[$this->titleIndex]);
		foreach($lines as $line => $content)
			Core::$score->setLine($player, $line, $content);
	}
}