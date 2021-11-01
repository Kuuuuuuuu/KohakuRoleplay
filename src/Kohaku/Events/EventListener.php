<?php

namespace Kohaku\Events;

use pocketmine\event\Listener;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\level\sound\GhastShootSound;
use _64FF00\PurePerms\event\PPGroupChangedEvent;
use pocketmine\math\Vector3;
use Kohaku\utils\Entities\{KFCCustomer, Seller};
use pocketmine\entity\Skin;
use Kohaku\utils\FormAPI\SimpleForm;
use Kohaku\Arena\Arena;
use onebone\economyapi\EconomyAPI;
use Kohaku\Task\JailTask;
use pocketmine\level\{Position, Level};
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\block\{BlockBurnEvent, BlockBreakEvent, BlockPlaceEvent};
use pocketmine\event\entity\{ProjectileLaunchEvent, EntityDamageEvent, EntityDamageByEntityEvent, EntityLevelChangeEvent};
use pocketmine\event\player\{PlayerToggleSprintEvent, PlayerJumpEvent, PlayerCreationEvent, PlayerPreLoginEvent, PlayerCommandPreprocessEvent, PlayerChatEvent, PlayerMoveEvent, PlayerQuitEvent, PlayerJoinEvent, PlayerLoginEvent, PlayerDeathEvent, PlayerInteractEvent, PlayerRespawnEvent, PlayerDropItemEvent, PlayerExhaustEvent, PlayerItemHeldEvent, PlayerChangeSkinEvent};
use pocketmine\utils\{TextFormat as Color, Config};
use Kohaku\Task\{VanishTask, HideTask, ScoreboardTask, CooldownTask};
use Kohaku\{KohakuPlayer, Core};
use pocketmine\{Server, Player};
use pocketmine\command\{Command, CommandSender};
use pocketmine\item\{EnderPearl, Item, ItemIds};
use pocketmine\nbt\tag\{ShortTag, ListTag, FloatTag, DoubleTag, CompoundTag};
use pocketmine\network\mcpe\protocol\{DisconnectPacket, MovePlayerPacket, PlaySoundPacket, AddActorPacket};
use pocketmine\entity\{EffectInstance, Entity, Effect};
use pocketmine\item\enchantment\{Enchantment, EnchantmentInstance};
use pocketmine\event\server\{DataPacketSendEvent, DataPacketReceiveEvent, QueryRegenerateEvent};
use pocketmine\network\mcpe\protocol\{LevelSoundEventPacket, InventoryTransactionPacket, ContainerClosePacket, LoginPacket};

class EventListener implements Listener {
	
	/**
	* @var Core
    */
    
    private $listener;
    
    /**
	* @var array
    */
    
    protected array $bannedCommands = [];

    /**
    * @param Core $listener
    */
    
    public function __construct(Core $listener)  {
        $listener = $this->listener;
        $this->bannedCommands = Core::getInstance()->getConfig()->get("banned-commands", []);
        $capes = new Config(Core::getInstance()->getDataFolder() . "config.yml", Config::YAML);
        if(is_array($capes->get("standard_capes"))) {
            foreach($capes->get("standard_capes") as $cape){
            $this->saveResource("$cape.png");
         }
         $capes->set("standard_capes", "done");
         $capes->save();
        }
    }
    
     /**
    * @param Core $listener
    */
    
    private function getListener() {
        return $this->listener;
    }
    
    public function PlayerDeath(PlayerDeathEvent $ev){
    	$ev->setKeepInventory(true);
    }
    
    public function onEntityDamageEvent(EntityDamageByEntityEvent $event): void{
    	$damager = $event->getDamager();
        $player = $event->getEntity();
        if($player instanceof Player and $damager instanceof Player) {
        	if(isset(Core::getInstance()->JailAdmin[$damager->getName()])) {
            	if(Core::getInstance()->JailAdmin[$damager->getName()] === "yes") {
                    Core::getInstance()->Jail[$player->getName()] = "yes";
                    Core::getInstance()->JailAdmin[$damager->getName()] = "no";
                    Server::getInstance()->broadcastMessage(Core::getInstance()->getPrefixCore() . "§l§fผู้คุม " . $damager->getName() . " §cได้จับ " . $player->getName() . " §cแล้ว");
                    Core::getInstance()->eco->addMoney($damager, 250);
                    Core::getInstance()->eco->reduceMoney($player, 350);
                 }
            }
        }
   }
        
    /**
   * @param PlayerInteractEvent $event
   * @priority LOWEST
   * @ignoreCancelled true
   */

   public function onProjectile(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $inv = $player->getInventory();
        $name = $player->getName();
         if($item->getCustomName() === "Phone") {
        	Core::$form->openPhoneMessage($player);
        }
        if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
            if($item->getCustomName() === "AK47") {
               if($inv->contains(Item::get(262, 0, 2))){
                   $nbt = new CompoundTag("", ["Pos" => new ListTag("Pos", [new DoubleTag("", $player->x), new DoubleTag("", $player->y + $player->getEyeHeight()), new DoubleTag("", $player->z)]), "Motion" => new ListTag("Motion", [new DoubleTag("", -\sin($player->yaw / 180 * M_PI) * \cos($player->pitch / 180 * M_PI)), new DoubleTag ("", -\sin($player->pitch / 180 * M_PI)), new DoubleTag("", \cos($player->yaw / 180 * M_PI) * \cos($player->pitch / 180 * M_PI))]), "Rotation" => new ListTag("Rotation", [new FloatTag("", $player->yaw), new FloatTag("", $player->pitch)])]);
                   $f = 3;
                   $entity = Entity::createEntity("Arrow", $player->getlevel(), $nbt, $player);
                   $entity->setMotion($entity->getMotion()->multiply($f));
                   $entity2 = Entity::createEntity("Arrow", $player->getlevel(), $nbt, $player);
                   $entity2->setMotion($entity->getMotion()->multiply($f));
                   $entity->spawnToAll();
                   $entity2->spawnToAll();
                   $inv->removeItem(Item::get(262, 0, 2));
                   Core::$utils->playSound("firework.blast", $player, 500, 2);
                 } else {
                   $player->sendTip("§cAll of Ammo");
                   Core::$utils->playSound("random.click", $player, 500, 0.3);
                 }
             }
        }
   }
   /**
    * @param DataPacketSendEvent $event
    * @priority LOWEST
    * @ignoreCancelled true
 */
 
	public function onDisconnectPacket(DataPacketSendEvent $event){
		$packet = $event->getPacket();
		$player = $event->getPlayer();
		if($packet instanceof DisconnectPacket and $packet->message === "Internal server error"){
			$packet->message = ("§bGuardian\n§cYou have encountered a bug.\n§fContact us: §bdiscord.dripsquad.ga\n§6Omlet Arcade: §enotkungz1");
		} else if($packet instanceof DisconnectPacket and $packet->message === "Server is white-listed"){
			$packet->message = ("§bGuardian\n§cWe are currently whitelisted, check back shortly.\n§fDiscord: §bdiscord.dripsquad.ga\n§6Omlet Arcade: §enotkungz1");
		} else if($packet instanceof DisconnectPacket and $packet->message === "Server closed"){
			$packet->message = ("§bGuardian\n§cServer Closed");
		}
	}

   /**
    * @param LevelLoadEvent $event
    * @priority MONITOR
    * @ignoreCancelled true
 */
   
   public function onLevelLoadEvent(LevelLoadEvent $event){
        $world = $event->getLevel();
        $world->setTime(0);
        $world->stopTime();
    }

	/**
    * @param DataPacketReceiveEvent $event
    * @priority LOWEST
    * @ignoreCancelled true
    */

    public function PacketReceived(DataPacketReceiveEvent $event) {
        $player = $event->getPlayer();
        $packet = $event->getPacket();
         if($packet instanceof LoginPacket)  {
         	if($packet->clientData["CurrentInputMode"] !== null and $packet->clientData["DeviceOS"] !== null and $packet->clientData["DeviceModel"] !== null){
			     Core::getInstance()->controls[$packet->username ?? "Unknown"] = $packet->clientData["CurrentInputMode"];
		         Core::getInstance()->os[$packet->username ?? "Unknown"] = $packet->clientData["DeviceOS"];
		         Core::getInstance()->device[$packet->username ?? "Unknown"] = $packet->clientData["DeviceModel"];
             }
             $players = $event->getPacket()->username;
             Core::getInstance()->fakeOs[strtolower($players)] = "Normal";
             $deviceOS = (int)$packet->clientData["DeviceOS"];
             $deviceModel = (string)$packet->clientData["DeviceModel"];
             if ($deviceOS !== 1)  {
                  return;
             }
            $name = explode(" ", $deviceModel);
             if (!isset($name[0])) {
                  return;
              }
             $check = $name[0];
             $check = strtoupper($check);
             if($check !== $name[0]) {
                 $players = $event->getPacket()->username;
                 Server::getInstance()->broadcastMessage(Core::getInstance()->getPrefixCore() . "§e" . $players . " §cUsing §aToolbox. Please Avoid that Player!");
                 Core::getInstance()->fakeOs[strtolower($players)] = "Toolbox";
             }
         }
     }
     
     public function onMove(PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();
        $block = $player->getLevel()->getBlock(new Vector3($player->x, $player->y - 0.5, $player->z));
        if($block->getId() === 41) { //gold_block 
            if(!isset(Core::getInstance()->Rob[$name])) {
           	Core::getInstance()->Rob[$name] = "yes";
            } else {
               Core::getInstance()->Rob[$name] = "yes";
            } 
	    } else {
            if($block->getId() !== 41) {
		        if(!isset(Core::getInstance()->Rob[$name])) {
               	Core::getInstance()->Rob[$name] = "no";
                   Core::getInstance()->RobProcess[$name] = 0;
                 } else {
                   Core::getInstance()->Rob[$name] = "no";
                   Core::getInstance()->RobProcess[$name] = 0;
                 }
             }
             if($block->getId() === 152) { //redstone_block 
             	if(isset(Core::getInstance()->DirtyMoney[$name])) {
        	         if(!isset(Core::getInstance()->WashingMoney[$name])) {
       	             Core::getInstance()->WashingMoney[$name] = "yes";
                      } else {
                        Core::getInstance()->WashingMoney[$name] = "yes";
                      }
                 }
            } else {
               if($block->getId() !== 152) {
                   if(!isset(Core::getInstance()->WashingMoney[$name])) {
                       Core::getInstance()->WashingMoney[$name] = "no";
                       Core::getInstance()->WashingProcess[$name] = 0;
                    } else {
                       Core::getInstance()->WashingMoney[$name] = "no";
                       Core::getInstance()->WashingProcess[$name] = 0;
                    }
               }
               if($block->getId() === 42) { //iron_block 
               	if(isset(Core::getInstance()->getApple[$name])) {
        	           if(!isset(Core::getInstance()->getApple[$name])) {
       	               Core::getInstance()->getApple[$name] = "yes";
                        } else {
                          Core::getInstance()->getApple[$name] = "yes";
                        }
                    }
                } else {
                   if($block->getId() !== 42) {
                       if(!isset(Core::getInstance()->getApple[$name])) {
                          Core::getInstance()->getApple[$name] = "no";
                          Core::getInstance()->getAppleProcess[$name] = 0;
                       } else {
                          Core::getInstance()->getApple[$name] = "no";
                          Core::getInstance()->getAppleProcess[$name] = 0;
                        }
                    }
                    if($block->getId() === 133) { //emerald_block 
                    	if(isset(Core::getInstance()->getWeed[$name])) {
        	               if(!isset(Core::getInstance()->getWeed[$name])) {
       	                   Core::getInstance()->getWeed[$name] = "yes";
                           } else {
                              Core::getInstance()->getWeed[$name] = "yes";
                           }
                       }
                   } else {
                       if($block->getId() !== 133) {
                           if(!isset(Core::getInstance()->getWeed[$name])) {
                              Core::getInstance()->getWeed[$name] = "no";
                              Core::getInstance()->getWeedProcess[$name] = 0;
                           } else {
                              Core::getInstance()->getWeed[$name] = "no";
                              Core::getInstance()->getWeedProcess[$name] = 0;
                           }
                       }
                       if($block->getId() === 170) { //Hay bale
                       	if(isset(Core::getInstance()->getWheat[$name])) {
        	                   if(!isset(Core::getInstance()->getWheat[$name])) {
       	                       Core::getInstance()->getWheat[$name] = "yes";
                               } else {
                                  Core::getInstance()->getWheat[$name] = "yes";
                               }
                           }
                      } else {
                         if($block->getId() !== 170) {
                              if(!isset(Core::getInstance()->getWheat[$name])) {
                                 Core::getInstance()->getWheat[$name] = "no";
                                 Core::getInstance()->getWheatProcess[$name] = 0;
                               } else {
                                 Core::getInstance()->getWheat[$name] = "no";
                                 Core::getInstance()->getWheatProcess[$name] = 0;
                               }
                           }
                           if($block->getId() === 22) { //Lapis Block
                           	if(isset(Core::getInstance()->getFish[$name])) {
        	                       if(!isset(Core::getInstance()->getFish[$name])) {
       	                           Core::getInstance()->getFish[$name] = "yes";
                                   } else {
                                      Core::getInstance()->getFish[$name] = "yes";
                                   }
                               }
                           } else {
                              if($block->getId() !== 22) {
                                  if(!isset(Core::getInstance()->getFish[$name])) {
                                     Core::getInstance()->getFish[$name] = "no";
                                     Core::getInstance()->getFishProcess[$name] = 0;
                                  } else {
                                     Core::getInstance()->getFish[$name] = "no";
                                     Core::getInstance()->getFishProcess[$name] = 0;
                                  }
                               }
                           }
                       }
                   }
               } 
           }
       }
   }
       
    /**
    * @param PlayerPreLoginEvent $event
    * @priority LOWEST
    * @ignoreCancelled true
 */
      
   public function onPlayerLogin(PlayerPreLoginEvent $event){
		$player = $event->getPlayer();
		$banplayer = $player->getName();
		$banInfo = Core::getInstance()->db->query("SELECT * FROM banPlayers WHERE player = '$banplayer';");
		$array = $banInfo->fetchArray(SQLITE3_ASSOC);
		foreach(Server::getInstance()->getOnlinePlayers() as $p){
			if($p !== $player and strtolower($player->getName()) === strtolower($p->getName())){
			    $event->setCancelled(true);
				$player->kick("§bGuardian §f>> §cYou Already Logged in", false);
			 }
		} 
        if (!empty($array)) {
			$banTime = $array["banTime"];
			$reason = $array["reason"];
			$staff = $array["staff"];
			$now = time();
			if($banTime > $now){
				$remainingTime = $banTime - $now;
				$day = floor($remainingTime / 86400);
				$hourSeconds = $remainingTime % 86400;
				$hour = floor($hourSeconds / 3600);
				$minuteSec = $hourSeconds % 3600;
				$minute = floor($minuteSec / 60);
				$remainingSec = $minuteSec % 60;
				$second = ceil($remainingSec);
				$player->kick(str_replace(["{day}", "{hour}", "{minute}", "{second}", "{reason}", "{staff}"], [$day, $hour, $minute, $second, $reason, $staff], Core::getInstance()->message["LoginBanMessage"]), false);
			} else {
				Core::getInstance()->db->query("DELETE FROM banPlayers WHERE player = '$banplayer';");
			}
		}
	}
	
	/**
    * @param PlayerJoinEvent $event
    * @priority HIGHEST
    * @ignoreCancelled true
    */

    public function PlayerJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();
        Core::getInstance()->JailAdmin[$name] = "no";
        $pk = new \pocketmine\network\mcpe\protocol\GameRulesChangedPacket();
        $pk->gameRules = ["doimmediaterespawn" => [1, true, false]];
        $player->sendDataPacket($pk);
        $event->setJoinMessage("§f[§a+§f]§e " . $name);
        $player->removeAllEffects();
        $player->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
        Core::getinstance()->getScheduler()->scheduleRepeatingTask(new ScoreboardTask(Core::getInstance(), $player), 20);
        $player->sendMessage(Core::getInstance()->getPrefixCore() . "§eLoading Player Data");
        $player->setGamemode(0);
        Core::$utils->Lightning($player);
        Core::getInstance()->BuildingMode[$name] = "no";
    }
     
     /**
     * @param PlayerQuitEvent $event
     * @priority LOWEST
     * @ignoreCancelled true
     */

    public function PlayerQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();
        $event->setQuitMessage("§f[§c-§f]§e " . $name);
        Core::$utils->Lightning($player);
   }

   /**
    * @param BlockBreakEvent $ev
    * @priority LOWEST
    * @ignoreCancelled true
    */
		
    public function onBreak(BlockBreakEvent $ev)  {
        $player = $ev->getPlayer();
        $name = $player->getName();
        if(!isset(Core::getInstance()->BuildingMode[$name])) {
        	Core::getInstance()->BuildingMode[$name] = "no";
        }
        if(isset(Core::getInstance()->BuildingMode[$name])) {
           if(!$player->isOp() or Core::getInstance()->BuildingMode[$name] === "no"){
          	$player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou Cannot Break blocks");
              $ev->setCancelled(true);
           }
        }
    }
    
    /**
    * @param BlockPlaceEvent $ev
    * @priority LOWEST
    * @ignoreCancelled true
    */
    
    public function onPlace(BlockPlaceEvent $ev) {
        $player = $ev->getPlayer();
        $name = $player->getName();
        $block = $ev->getBlock();
        if(!isset(Core::getInstance()->BuildingMode[$name])) {
        	Core::getInstance()->BuildingMode[$name] = "no";
        }
        if(Core::getInstance()->RoleEdit === false) {
            if($block->getId() === 41) {
            	$ev->setCancelled(true);
                $player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou Cannot Place this blocks");
            }
            if($block->getId() === 152) {
            	$ev->setCancelled(true);
                $player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou Cannot Place this blocks");
            }
            if($block->getId() === 42) {
            	$ev->setCancelled(true);
                $player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou Cannot Place this blocks");
            }
            if($block->getId() === 133) {
               $ev->setCancelled(true);
               $player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou Cannot Place this blocks");
            }
            if($block->getId() === 170) {	
               $ev->setCancelled(true);
               $player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou Cannot Place this blocks");
            }
        }
        if(isset(Core::getInstance()->BuildingMode[$name])) {
           if(!$player->isOp() or Core::getInstance()->BuildingMode[$name] === "no"){
          	$player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou Cannot Place blocks");
              $ev->setCancelled(true);
           }
        }
    }
    
    /**
    * @param PlayerDropItemEvent $ev
    * @priority LOWEST
    * @ignoreCancelled true
    */
    
    public function onDrop(PlayerDropItemEvent $ev) {
        $player = $ev->getPlayer();
        $item = $ev->getItem();
        if ($item->getCustomName() === "Phone") {
            $ev->setCancelled(true);
            $player->sendMessage(Core::getInstance()->getPrefixCore() . "§l§cคุณไม่สามารถ โยนโทรศัพท์ได้");
         }
    }
    
    /**
    * @param PlayerRespawnEvent $ev
    * @priority LOWEST
    * @ignoreCancelled true
    */
    
     public function onRespawn(PlayerRespawnEvent $ev) {
        $player = $ev->getPlayer();
        $player->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
        $player->setGamemode(0);
        $player->setScale(1);
        $player->setMaxHealth(20);
        $player->setHealth(20);
        Core::getInstance()->eco->reduceMoney($player, 100);
        $player->sendMessage(Core::getInstance()->getPrefixCore(). "§l§aคุณเงินลด 100 เพราะ คุณตาย");
        Core::getInstance()->Respawn[$player->getName()] = Core::getInstance()->MaxRespawnTime;
      }
      
      public function onFunction(EntityDamageByEntityEvent $ev){
		$npc = $ev->getEntity();
		$player = $ev->getDamager();
		if($npc instanceof KFCCustomer && $player instanceof Player){
			$ev->setCancelled(true);
			Core::$form->KFCForm($player);
		}
		if($npc instanceof Seller && $player instanceof Player){
			$ev->setCancelled(true);
			Core::$form->SellerForm($player);
		 }
	}
	
}