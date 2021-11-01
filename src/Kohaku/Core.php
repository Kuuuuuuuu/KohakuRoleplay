<?php

declare(strict_types=1);

namespace Kohaku;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\entity\Entity;
use pocketmine\scheduler\Task;
use pocketmine\{Player, Server};
use pocketmine\level\{Position, Level};
use pocketmine\utils\{Config, TextFormat};
use pocketmine\command\{CommandSender, Command};
use Kohaku\Events\{EventListener};
use Kohaku\utils\Entities\{KFCCustomer, Seller};
use Kohaku\Execute\{CoreCommand, JailCommand, ItemCommand, VersionCommand, PlayerInfoCommand, TbanCommand, TcheckCommand, AnnounceCommand, TpsCommand};
use Kohaku\Task\{ScoreboardTask, JailTask, WheatTask, FishTask, MarketTask, NPCTask, WeedTask, AppleTask, WashMoneyTask, RobTask, RespawnTask, TagTask, ClearEntitiesTask, BroadcastTask};
use Kohaku\utils\{FormUtils, ChatSystem, Scoreboards, Utils, BanUtils};

class Core extends PluginBase {
	
	private int $loadworld = 0;
	protected static $plugin;
	public Config $cfg;
    public array $cd = [];
    public array $skin = [];
    
    /**RolePlay Plugins**/
    public int $ClearLagg = 0;
    public array $Jail = [];
    public array $JailTime = [];
    public array $JailAdmin = [];
    public array $Respawn = [];
    public bool $JailEnable = false;
    public array $DirtyMoney = [];
    public array $RobProcess = [];
    public array $Rob = [];
    public array $WashingProcess = [];
    public array $getWheatProcess = [];
    public array $WashingMoney = [];
    public array $targetPlayer = [];
	public array $targetPhone = [];
	public array $getApple = [];
	public array $getWeed = [];
	public array $getWheat = [];
	public array $getFish = [];
	public int $MaxWashingProcess = 200;
	public int $WeedPrice = 0;
	public int $ApplePrice = 0;
	public int $WheatPrice = 0;
	public int $FishPrice = 0;
	public int $MaxJailTime = 30;
	public int $MaxRespawnTime = 10;
	public int $MaxRobProgress = 200;
	public int $MaxAppleProcess = 64;
	public int $MaxWeedProcess = 64;
	public int $MaxFishProcess = 64;
	public int $MaxWheatProcess = 64;
	public bool $RoleEdit = false;
	/**end of data**/
	
    public array $worlds = [];
	public array $BuildingMode = [];
	public array $taggedPlayers = [];
	public array $os = [];
    public array $fakeOs = [];
    public array $device = []; 
    public array $controls = [];
    public array $allCtrs = ["Unknown", "Mouse", "Touch", "Controller"];
    public array $listOfOs = ["Unknown", "Android", "iOS", "macOS", "FireOS", "GearVR", "HoloLens", "Windows10", "Windows", "EducalVersion", "Dedicated", "PlayStation", "Switch", "XboxOne"];
	public static $ban = null;
	public static $utils = null;
	public static $score = null;
	public static $form = null;
   
	public function onLoad() : void {
	    Core::$utils = new Utils($this);
	    Core::$ban = new BanUtils($this); //useless
        Core::$score = new Scoreboards($this);
        Core::$form = new FormUtils($this);
	    self::$plugin = $this;
    }
	
	public function onEnable() : void { 
		@mkdir($this->getDataFolder() . "pkdata/");
		$this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		$this->getLogger()->info("\n\n\n              [" . TextFormat::BOLD . TextFormat::RED . "Kohaku" . TextFormat::WHITE . "Core" . "]\n\n");
		$this->saveDefaultConfig();
		$this->registerevent();
		$this->scheduleTask();
		$this->registercommands();
        $this->reloadConfig();
		$this->registerentity();
		$this->saveDefaultConfig();
		$this->registerconfig();
		$this->getServer()->getNetwork()->setName("§bRoleplay");
		foreach(glob($this->getServer()->getDataPath() . "worlds/*") as $world) {
            $world = str_replace($this->getServer()->getDataPath() . "worlds/", "", $world);
            if($this->getServer()->isLevelLoaded($world)){
                continue;
            }
            $this->getServer()->loadLevel($world);
            $this->getLogger()->critical("§aPass √ > §eLoad World: §f".  $world);
        }
        foreach($this->getServer()->getLevels() as $world){
        	$this->loadworld++;
        	$this->getLogger()->critical("§aPass √ > §eLock Time World: §f". $this->loadworld);
        	$world->setTime(0);
            $world->stopTime();
        }
	}

	public function onDisable(): void {
        $this->taggedPlayers = [];
        $this->getLogger()->critical("§cDisable √ > §eClose All Events");
     }

	private function registerentity() : void {
		Entity::registerEntity(KFCCustomer::class, true);
        Entity::registerEntity(Seller::class, true);
		$this->getLogger()->critical("§aPass √ > §eRegister Entities");
	}
	
	public function registerconfig() : void  {
		$this->getLogger()->critical("§aPass √ > §eRegister Configs");
		$this->cfg = $this->getConfig();
		$this->db = new \SQLite3($this->getDataFolder() . "Ban.db");
		$this->db->exec("CREATE TABLE IF NOT EXISTS banPlayers(player TEXT PRIMARY KEY, banTime INT, reason TEXT, staff TEXT);");
		$this->message = (new Config($this->getDataFolder() . "bantext.yml", Config::YAML, array("BroadcastBanMessage" => "§f––––––––––––––––––––––––\n§ePlayer §f: §c{player}\n§eHas banned: §c{day}§eD §f| §c{hour}§eH §f| §c{minute}§eM\n§eReason: §c{reason}\n§f––––––––––––––––––––––––§f", "KickBanMessage" => "§bGuardian\n§cYou Are Banned\n§6Reason : §f{reason}\n§6Unban At §f: §e{day} D §f| §e{hour} H §f| §e{minute} M", "LoginBanMessage" => "§bGuardian\n§cYou Are Banned\n§6Reason : §f{reason}\n§6Unban At §f: §e{day} D §f| §e{hour} H §f| §e{minute} M", "BanMyself" => "§cYou can't ban yourself", "BanModeOn" => "§aBan mode on", "BanModeOff" => "§cBan mode off", "NoBanPlayers" => "§aNo ban players", "UnBanPlayer" => "§b{player} §ahas been unban", "AutoUnBanPlayer" => "§a{player} Has Auto Unban Already!", "BanListTitle" => "§bZelda §eBanSystem", "BanListContent" => "§c§lChoose player", "PlayerListTitle" => "§bZelda §eBanSystem", "PlayerListContent" => "§c§lChoose Player", "InfoUIContent" => "§bInformation: \nDay: §a{day} \n§bHour: §a{hour} \n§bMinute: §a{minute} \n§bSecond: §a{second} \n§bReason: §a{reason}", "InfoUIUnBanButton" => "§aUnban")))->getAll();
		@mkdir($this->getDataFolder());
		@mkdir($this->getDataFolder() . "capes/");
	}

    private function registerevent() : void {
    	$this->getLogger()->critical("§aPass √ > §eRegister Events");
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
	}
    
	private function registercommands() : void {
		$this->getLogger()->critical("§aPass √ > §eRegister Commands");
		$this->getServer()->getCommandMap()->register("tps", new TpsCommand($this));
		$this->getServer()->getCommandMap()->register("announce", new AnnounceCommand($this));
		$this->getServer()->getCommandMap()->register("tban", new TbanCommand($this));
		$this->getServer()->getCommandMap()->register("tcheck", new TcheckCommand($this));
		$this->getServer()->getCommandMap()->register("pinfo", new PlayerInfoCommand($this));
		$this->getServer()->getCommandMap()->register("version", new VersionCommand($this));
		$this->getServer()->getCommandMap()->register("item", new ItemCommand($this));
		$this->getServer()->getCommandMap()->register("jail", new JailCommand($this));
		$this->getServer()->getCommandMap()->register("Core", new CoreCommand($this));
	}
	
	private function scheduleTask() : void {
		$this->getLogger()->critical("§aPass √ > §eRegister Tasks");
	    $this->getScheduler()->scheduleDelayedRepeatingTask(new BroadcastTask($this), 150, 8000);
		$this->getScheduler()->scheduleRepeatingTask(new ClearEntitiesTask($this), 20);
		$this->getScheduler()->scheduleRepeatingTask(new RespawnTask($this), 20);
		$this->getScheduler()->scheduleRepeatingTask(new RobTask($this), 20);
		$this->getScheduler()->scheduleRepeatingTask(new AppleTask($this), 20);
		$this->getScheduler()->scheduleRepeatingTask(new WheatTask($this), 20);
		$this->getScheduler()->scheduleRepeatingTask(new FishTask($this), 20);
		$this->getScheduler()->scheduleRepeatingTask(new JailTask($this), 20);
		$this->getScheduler()->scheduleRepeatingTask(new WeedTask($this), 20);
		$this->getScheduler()->scheduleRepeatingTask(new NPCTask($this), 20);
		$this->getScheduler()->scheduleRepeatingTask(new WashMoneyTask($this), 20);
		$this->getScheduler()->scheduleRepeatingTask(new TagTask($this), 20);
		$this->getScheduler()->scheduleRepeatingTask(new MarketTask($this), 16000);
    }
	
	/**
	 * @return self
	 */
	
	public static function getInstance() {
		return self::$plugin;
	}
	
	
	/**
	 * @return string
	 */
	
	public function getPrefixCore() : string {
		return "§aRolePlay §e>§f ";
	}
	
	/**
	 * @return Utils
	 */
	
	public static function getUtils() : Utils {
		return Core::$utils;
	}
	
	/**
	 * @return BanUtils
	 */
	
	public static function getBanUtils() : BanUtils {
		return Core::$ban;
	}
	
	/**
	 * @return FormUtils
	 */
	
	public function getFormUtils() : FormUtils {
		return Core::$form;
	}
	
	/**
	 * @return string
	 */
	
	public function getPurePerms() {
		return Server::getInstance()->getPluginManager()->getPlugin("PurePerms");
	}
}
