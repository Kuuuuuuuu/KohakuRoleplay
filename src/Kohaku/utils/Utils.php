<?php

namespace Kohaku\utils;

use pocketmine\network\mcpe\protocol\{PlaySoundPacket, AddActorPacket};
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\{Server, Player};
use pocketmine\entity\{Entity};
use pocketmine\math\Vector3;
use pocketmine\entity\Skin;
use pocketmine\item\{Item, Potion};
use pocketmine\item\enchantment\{Enchantment, EnchantmentInstance};
use pocketmine\level\Location;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\level\Position;
use Kohaku\Core;
use Kohaku\Arena\{Sumo};
use Kohaku\Task\WinningTask;
use Kohaku\Entity\CustomFallingWoolBlock;

class Utils {
	
	public function getItem(Player $player){
		Enchantment::registerEnchantment(new Enchantment(100, "", 0, 0, 0, 1));
        $enchantment = Enchantment::getEnchantment(100);
        $this->enchInst = new EnchantmentInstance($enchantment, 1);
        $item2 = Item::get(347);
        $item2->setCustomName("AK47");
        $item2->addEnchantment($this->enchInst);
        $player->getInventory()->setItem(8, $item2, true);
        $item = Item::get(247);
        $item->setCustomName("Phone");
        $item->addEnchantment($this->enchInst);
        $player->getInventory()->setItem(4, $item, true);
        
	}
	
    public function Lightning(Player $player) :void {
        $light = new AddActorPacket();
        $light->type = "minecraft:lightning_bolt";
        $light->entityRuntimeId = Entity::$entityCount++;
        $light->metadata = [];
        $light->motion = null;
        $light->yaw = $player->getYaw();
        $light->pitch = $player->getPitch();
        $light->position = new Vector3($player->getX(), $player->getY(), $player->getZ());
        Server::getInstance()->broadcastPacket($player->getLevel()->getPlayers(), $light);
        $block = $player->getLevel()->getBlock($player->getPosition()->floor()->down());
        $particle = new DestroyBlockParticle(new Vector3($player->getX(), $player->getY(), $player->getZ()), $block);
        $player->getLevel()->addParticle($particle);
        $sound = new PlaySoundPacket();
        $sound->soundName = "ambient.weather.lightning.impact";
        $sound->x = $player->getX();
        $sound->y = $player->getY();
        $sound->z = $player->getZ();
        $sound->volume = 1;
        $sound->pitch = 1;
        Server::getInstance()->broadcastPacket($player->getLevel()->getPlayers(), $sound);
    }
    
    public static function playSound(string $soundName, Position $position, int $volume = 500, float $pitch = 1){
                $pk = new PlaySoundPacket;
                $pk->soundName = $soundName;
                $pk->x = $position->x;
                $pk->y = $position->y;
                $pk->z = $position->z;
                $pk->volume = $volume;
                $pk->pitch = $pitch;
                $position->level->broadcastGlobalPacket($pk);
        }
    
    public function getPlayerOs(Player $player) : ? string{
        if(!isset(Core::getInstance()->os[$player->getName()]) or Core::getInstance()->os[$player->getName()]===null){
			return "Unknown";
		}
		return Core::getInstance()->listOfOs[Core::getInstance()->os[$player->getName()]];
	}

    public function getPlayerDevice(Player $player): ? string{
		if(!isset(Core::getInstance()->device[$player->getName()]) or Core::getInstance()->device[$player->getName()]===null){
			return "Unknown";
		}
		return Core::getInstance()->device[$player->getName()];
	}
	
   public function getPlayerControls(Player $player): ? string{
		if(!isset(Core::getInstance()->controls[$player->getName()]) or Core::getInstance()->controls[$player->getName()]===null){
			return "Unknown";
		}
		return Core::getInstance()->allCtrs[Core::getInstance()->controls[$player->getName()]];
	}

    public function setPlayerOs(Player $player, string $os){
        Core::getInstance()->os[strtolower($player->getName())] = $os;
    }
    
    public function getFakeOs(Player $player) : ? string{
        return Core::getInstance()->fakeOs[strtolower($player->getName())] ?? "Unknown";
    }

    public function setFakeOs(Player $player, string $fakeOs) : bool{
        if($fakeOs === "") return false;
        Core::getInstance()->fakeOs[strtolower($player->getName())] = $fakeOs;
        return true;
    }
    
    public function createCape($capeName){
        $path = Core::getInstance()->getDataFolder() . "capes/{$capeName}.png";
        $img = @imagecreatefrompng($path);
        $bytes = "";
        $l = (int)@getimagesize($path)[1];
        for ($y = 0; $y < $l; $y++) {
            for ($x = 0; $x < 64; $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $bytes;
    }
    
    public function getCapes(){
        $list = array();
        foreach(scandir(Core::getInstance()->getDataFolder() . "capes") as $data){
              $dat = explode(".", $data);
              if($dat[1] === "png"){
                  array_push($list, $dat[0]);
             }
          }
          return $list;
    }
}