<?php

namespace Kohaku\Execute;

use Kohaku\Core;
use pocketmine\command\{CommandSender, PluginCommand};
use pocketmine\utils\TextFormat;
use pocketmine\Server;

class PlayerInfoCommand extends PluginCommand {

    private $plugin;

    public function __construct(Core $plugin) {
        parent::__construct("pinfo", $plugin);
        $this->setDescription("View Player Information");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
    	if(!isset($args[0])) {
            $sender->sendMessage(Core::getInstance()->getPrefixCore() . "§e" . $sender->getName() . " §aInfo");
            $sender->sendMessage("\n");
            $sender->sendMessage("§bPlayer §aDevice §f>>§e " . Core::$utils->getPlayerDevice($sender));
            $sender->sendMessage("§bPlayer §aOS §f>>§e " . Core::$utils->getPlayerOs($sender));
            $sender->sendMessage("§bPlayer §aController §f>>§e " . Core::$utils->getPlayerControls($sender));
            $sender->sendMessage("§bPlayer §aToolbox Check §f>>§e " . Core::$utils->getFakeOs($sender));
         } else {
             if(Server::getInstance()->getPlayer($args[0]) !== null) {
                  $target = Server::getInstance()->getPlayer($args[0]);
                  $sender->sendMessage(Core::getInstance()->getPrefixCore() . "§e" . $target->getName() . " §aInfo");
                  $sender->sendMessage("\n");
                  $sender->sendMessage("§bPlayer §aDevice§f >>§e " . Core::$utils->getPlayerDevice($target));
                  $sender->sendMessage("§bPlayer §aOS §f>>§e " . Core::$utils->getPlayerOs($target));
                  $sender->sendMessage("§bPlayer §aController §f>>§e " . Core::$utils->getPlayerControls($target));
                  $sender ->sendMessage("§bPlayer §aToolbox Check §f>>§e " . Core::$utils->getFakeOs($target));
               } else $sender->sendMessage("§cUser: §f$args[0] §cis not currently online");
            }
        return true;
    }
}