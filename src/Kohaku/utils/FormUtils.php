<?php

namespace Kohaku\utils;

use Kohaku\utils\FormAPI\{CustomForm, SimpleForm};
use pocketmine\{Player, Server};
use pocketmine\utils\{TextFormat as Color, Config};
use pocketmine\entity\Skin;
use pocketmine\item\{Item, ItemIds};
use Kohaku\utils\Entities\{CoreManager, KFCCustomer, Seller};
use Kohaku\Core;

class FormUtils {
	
	public function __construct(Core $plugin) {
		$this->plugin = $plugin;
     }
     
     public function openUnJail($player){
		$form = new SimpleForm(function (Player $player, $data = null){
			$target = $data;
			if($target === null){
				return true;
			}
			Core::getInstance()->JailTime[$target] = 0;
		});
		$form->setTitle(Core::getInstance()->getPrefixCore() . "§aUnJail");
		foreach(Server::getInstance()->getOnlinePlayers() as $online){
			$form->addButton($online->getName(), -1, "", $online->getName());
		}
		$form->sendToPlayer($player);
		return $form;
	}
	
	public function openPhoneMessage($player){
		$form = new SimpleForm(function (Player $player, $data = null){
			$target = $data;
			if($target === null){
				return true;
			}
			Core::getInstance()->targetPhone[$player->getName()] = $target;
			$this->SendMessageForm($player);
		});
		$form->setTitle(Core::getInstance()->getPrefixCore() . "§aMessage");
		foreach(Server::getInstance()->getOnlinePlayers() as $online){
			$form->addButton($online->getName(), -1, "", $online->getName());
		}
		$form->sendToPlayer($player);
		return $form;
	}
     
     public function SendMessageForm($player) {
        $form = new CustomForm(function (Player $player, $data = null) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            if(isset(Core::getInstance()->targetPhone[$player->getName()])){
				if(Core::getInstance()->targetPhone[$player->getName()] == $player->getName()){
					$player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou can't Send Message to Your Self!");
					return true;
				}
			}
            foreach(Server::getInstance()->getOnlinePlayers() as $players){
            	if($players->getName() === Core::getInstance()->targetPhone[$player->getName()]) {
            	    $players->sendMessage($player->getName() . " §e> §t" . $data[0]);
                    $players->sendTitle("§aNew Message");
                    $players->sendSubTitle("§eCheck in Chat");
                 }
            }
            
            });
           $form->setTitle("§aRolePlay §ePhone");
           $form->addInput("§bSend Message to " . Core::getInstance()->targetPhone[$player->getName()]);
           $form->sendToPlayer($player);
         }

	public function openCapesUI($player) {
        $form = new SimpleForm(function (Player $player, $data = null) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                  $pdata = new Config(Core::getInstance()->getDataFolder() . "data.yml", Config::YAML);
                  $oldSkin = $player->getSkin();
                  $setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), "", $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
                  $player->setSkin($setCape);
                  $player->sendSkin();
                  if($pdata->get($player->getName()) !== null){
                     $pdata->remove($player->getName());
                     $pdata->save();
                  }     
                  $player->sendMessage(Core::getInstance()->getPrefixCore() . "§aYour Skin has been resetted!");
                  break;
                  case 1:
                  $this->openCapeListUI($player);
                  break;
                 }
            });
           $form->setTitle("§aRolePlay §eCapes");
           $form->addButton("§0Remove your Cape", 0, "textures/blocks/barrier");
           $form->addButton("§eChoose a Cape", 0, "textures/items/snowball");
           $form->sendToPlayer($player);
         }
                        
    public function openCapeListUI($player){
        $form = new SimpleForm(function (Player $player, $data = null) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            $cape = $data;
            $pdata = new Config(Core::getInstance()->getDataFolder() . "data.yml", Config::YAML);
             if(!file_exists(Core::getInstance()->getDataFolder() . "capes/" . $data . ".png")) {
                 $player->sendMessage(Core::getInstance()->getPrefixCore() . "The choosen Skin is not available!");
                }else{
                     if (!$player->hasPermission("$cape.cape")) {
                         $player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou dont have Permissions for this action!");
                       } else {
                        $oldSkin = $player->getSkin();
                        $capeData = Core::$utils->createCape($cape);
                        $setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $capeData, $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
                        $player->setSkin($setCape);
                        $player->sendSkin();
                        $msg = "§aRolePlay§f >> §aYou Use {name} §fCape";
                        $msg = str_replace("{name}", $cape, $msg);
                        $player->sendMessage($msg);
                        $pdata->set($player->getName(), $cape);
                        $pdata->save();
                     }
                 }
            });
           $form->setTitle("§aRolePlay §eCapes");
           foreach(Core::$utils->getCapes() as $capes){
              $form->addButton("$capes", -1, "", $capes, 0, "textures/blocks/glass_light_blue.png");
           }
        $form->sendToPlayer($player);
      }

  public function advanceban($player){
      $form = new CustomForm(function (Player $player, array $data = null){
      $result = $data;
      if($result === null){
        return true;
      } 
      if($result !== null){
         Core::getInstance()->targetPlayer[$player->getName()] = $data[0];
	     Core::$ban->openTbanUI($player);
         return true;
		}
     });
    $form->setTitle("§aRolePlay §cBanSystem");
    $form->addInput("§6Enter Player Name Here!");
    $form->sendToPlayer($player);
    return $form;
  }
     public function SellerForm($player) {
        $form = new SimpleForm(function (Player $player, $data = null) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
            	case 0:
                $inv = $player->getInventory();
                if($inv->contains(Item::get(260, 0, 64))){
                	Core::getInstance()->eco->addMoney($player, Core::getInstance()->ApplePrice);
                    $inv->removeItem(Item::get(260, 0, 64));
                    $player->sendMessage(Core::getInstance()->getPrefixCore() . "§aYou got: " . Core::getInstance()->ApplePrice . "$");
                 } else {
                   $player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou dont have enough Apple to sell");
                 }
                 break;
                 case 1:
                 $inv = $player->getInventory();
                 if($inv->contains(Item::get(464, 0, 64))){
                	Core::getInstance()->eco->addMoney($player, Core::getInstance()->WeedPrice);
                    $inv->removeItem(Item::get(464, 0, 64));
                    $player->sendMessage(Core::getInstance()->getPrefixCore() . "§aYou got: " . Core::getInstance()->WeedPrice . "$");
                  } else {
                    $player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou dont have enough Weed to sell");
                  }
                  break;
                  case 2:
                  $inv = $player->getInventory();
                  if($inv->contains(Item::get(349, 0, 64))){
                	Core::getInstance()->eco->addMoney($player, Core::getInstance()->FishPrice);
                    $inv->removeItem(Item::get(349, 0, 64));
                    $player->sendMessage(Core::getInstance()->getPrefixCore() . "§aYou got: " . Core::getInstance()->FishPrice . "$");
                  } else {
                    $player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou dont have enough Fish to sell");
                  }
                  break;
                  case 3:
                  $inv = $player->getInventory();
                  if($inv->contains(Item::get(292, 0, 64))){
                	Core::getInstance()->eco->addMoney($player, Core::getInstance()->WheatPrice);
                    $inv->removeItem(Item::get(292, 0, 64));
                    $player->sendMessage(Core::getInstance()->getPrefixCore() . "§aYou got: " . Core::getInstance()->WheatPrice . "$");
                  } else {
                    $player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou dont have enough Wheat to sell");
                  }
                  break;
            }
            });
           $form->setTitle("§aRolePlay §eSeller"); //improve add item pictures // Fix Random market Price
           $form->setContent("§eYour §6Money: §a" . Core::getInstance()->eco->myMoney($player));
           $form->addButton("§cApple\n§764×" . Core::getInstance()->ApplePrice . "$");
           $form->addButton("§aWeed\n§764×" . Core::getInstance()->WeedPrice . "$");
           $form->addButton("§bFish\n§764×" . Core::getInstance()->FishPrice . "$");
           $form->addButton("§eWheat\n§764×" . Core::getInstance()->WheatPrice . "$");
           $form->sendToPlayer($player);
         }
  
     public function KFCForm($player) {
        $form = new SimpleForm(function (Player $player, $data = null) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                $economy = Core::getInstance()->eco;
			    if($economy->myMoney($player) >= 25) {
				    $player->sendMessage(Core::getInstance()->getPrefixCore() . "§aBuy Complete");
				    $inventory = $player->getInventory();
		            $inventory->addItem(Item::get(366, 0, 1)); //Cooked Chicken
		            $inventory->sendContents($player);
		            $economy->reduceMoney($player, 25);
		            $this->KFCForm($player);
		         } else {
			       $player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou not have enough Money to buy");
			     }
			     break;
			     case 1:
			     $economy = Core::getInstance()->eco;
			     if($economy->myMoney($player) >= 10) {
				    $player->sendMessage(Core::getInstance()->getPrefixCore() . "§aBuy Complete");
				    $inventory = $player->getInventory();
		            $inventory->addItem(Item::get(282, 0, 1)); //Mushroom Soup
		            $inventory->sendContents($player);
		            $economy->reduceMoney($player, 10);
		            $this->KFCForm($player);
		         } else {
			       $player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou not have enough Money to buy");
			     }
			     break;
			     case 2:
			     $economy = Core::getInstance()->eco;
			     if($economy->myMoney($player) >= 15) {
				    $player->sendMessage(Core::getInstance()->getPrefixCore() . "§aBuy Complete");
				    $inventory = $player->getInventory();
		            $inventory->addItem(Item::get(350, 0, 1)); //cooked fish
		            $inventory->sendContents($player);
		            $economy->reduceMoney($player, 15);
		            $this->KFCForm($player);
		         } else {
			       $player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou not have enough Money to buy");
			     }
			     break;
			     case 3:
			     $economy = Core::getInstance()->eco;
			     if($economy->myMoney($player) >= 5) {
				    $player->sendMessage(Core::getInstance()->getPrefixCore() . "§aBuy Complete");
				    $inventory = $player->getInventory();
		            $inventory->addItem(Item::get(357, 0, 1)); //cookie
		            $inventory->sendContents($player);
		            $economy->reduceMoney($player, 5);
		            $this->KFCForm($player);
		         } else {
			       $player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou not have enough Money to buy");
			     }
			     case 4:
			     $economy = Core::getInstance()->eco;
			     if($economy->myMoney($player) >= 30) {
				    $player->sendMessage(Core::getInstance()->getPrefixCore() . "§aBuy Complete");
				    $inventory = $player->getInventory();
		            $inventory->addItem(Item::get(364, 0, 1)); //steak
		            $inventory->sendContents($player);
		            $economy->reduceMoney($player, 30);
		            $this->KFCForm($player);
		         } else {
			       $player->sendMessage(Core::getInstance()->getPrefixCore() . "§cYou not have enough Money to buy");
			     }
			     break;
               }
            });
           $form->setTitle("§aRolePlay §cKFC §fShop"); //improve add item pictures
           $form->setContent("§eYour §6Money: §a" . Core::getInstance()->eco->myMoney($player));
           $form->addButton("§6Cooked Chicken\n§725$\n");
           $form->addButton("§6Mushroom Soup\n§710$\n");
           $form->addButton("§6Cooked Fish\n§715$\n");
           $form->addButton("§6Cookies\n§75$\n");
           $form->addButton("§6Cooked Beef\n§730$\n");
           $form->sendToPlayer($player);
         }

    public function SettingsForm(Player $player) {
        $form = new SimpleForm(function (Player $player, int $data = null) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
             case 0:
             $this->openCapesUI($player);
             break;
             case 1:
             if(Core::getInstance()->BuildingMode[$player->getName()] == "yes") {
             	Core::getInstance()->BuildingMode[$player->getName()] = "no";
             	$player->sendMessage(Core::getInstance()->getPrefixCore() . "§eYou are no longer Building Mode now");
             } else {
             	Core::getInstance()->BuildingMode[$player->getName()] = "yes";
             	$player->sendMessage(Core::getInstance()->getPrefixCore() . "§eNow You are in Building Mode");
             }
             break;
             case 2:
             $this->advanceban($player);
             break;
             case 3:
             Core::$ban->openPlayerListUI($player);
             break;
             case 4:
             $slapper = new CoreManager();
		     $slapper->setKFCShop($player);
		     $player->sendMessage("OK");
		     break;
		     case 5:
		     $slapper = new CoreManager();
		     $slapper->setSeller($player);
		     $player->sendMessage("OK");
		     break;
		     case 6:
		     $npc = Server::getInstance()->getDefaultLevel()->getEntities();
				foreach ($npc as $entity){
					if($entity instanceof KFCCustomer){
						$entity->close();
						$player->sendMessage("OK");
					}
				}
			break;
			case 7:
		     $npc = Server::getInstance()->getDefaultLevel()->getEntities();
				foreach ($npc as $entity){
					if($entity instanceof Seller){
						$entity->close();
						$player->sendMessage("OK");
					}
				}
			break;
			case 8:
			$this->openUnJail($player);
			break;
            case 9:
            if(Core::getInstance()->RoleEdit === false) {
               Core::getInstance()->RoleEdit = true;
            } else {
               Core::getInstance()->RoleEdit = false;
            }
          }
	      break;
	      case 10:
	      if(Core::getInstance()->EnableJail === false) {
	          Core::getInstance()->EnableJail = true;
	      } else {
	          Core::getInstance()->EnableJail = false;
	      }
	    }
	    break;

        }
        });
        $form->setTitle("§aRolePlay §eSettings");
        $form->addButton("§6Change §eCapes", 0, "textures/ui/dressing_room_capes.png");
        $form->addButton("§6Building §eMode",0, "textures/items/diamond_pickaxe.png");
        $form->addButton("§6Advance §eBan" ,0, "textures/items/blaze_rod.png");
        $form->addButton("§6Quick §eBan" ,0, "textures/items/blaze_rod.png");
        $form->addButton("Spawn KFCCustomer");
        $form->addButton("Spawn Seller");
        $form->addButton("Remove KFCCustomer");
        $form->addButton("Remove Seller");
        $form->addButton("Unjail");
        $form->addButton("RoleEdit");
        $form->addButton("JailEnable")
        $form->sendToPlayer($player);
        return true;
    }
}