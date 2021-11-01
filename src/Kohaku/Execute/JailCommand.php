<?php

namespace Kohaku\Execute;

use Kohaku\Core;
use pocketmine\command\{CommandSender, PluginCommand};
use pocketmine\utils\TextFormat;
use pocketmine\Server;

class JailCommand extends PluginCommand {

    private $plugin;
    private $switch;

    public function __construct(Core $plugin) {
        parent::__construct("jail", $plugin);
        $this->setDescription("Jail Mode For Police");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
    	if(!$sender->hasPermission("core.jail")) $sender->sendMessage(Core::getInstance()->getPrefixCore(). "§cYou Not have any permission to use this Command!");
        if(Core::getInstance()->EnableJail === false) $sender->sendMessage(Core::getInstance()->getPrefixCore() . "§cJail Mode is not allowed now");
        if(Core::getInstance()->EnableJail === true) {
           if($sender->hasPermission("core.jail")){
              if(isset(Core::getInstance()->JailAdmin[$sender->getName()])) {
   	             if(Core::getInstance()->JailAdmin[$sender->getName()] === "yes") {
   	                $sender->sendMessage(Core::getInstance()->getPrefixCore() . "§bJail Mode: §cOFF");
                    Core::getInstance()->JailAdmin[$sender->getName()] = "no";
                 } else {
              	   $sender->sendMessage(Core::getInstance()->getPrefixCore() . "§bJail Mode: §aON");
             	   Core::getInstance()->JailAdmin[$sender->getName()] = "yes";
                 }
              } else if(!isset(Core::getInstance()->JailAdmin[$sender->getName()])) {
       	        Core::getInstance()->JailAdmin[$sender->getName()] = "no";
       	      }
           }
        }
    }
}