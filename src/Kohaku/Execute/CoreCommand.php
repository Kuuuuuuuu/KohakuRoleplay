<?php

namespace Kohaku\Execute;

use Kohaku\Core;
use pocketmine\command\{CommandSender, PluginCommand};
use pocketmine\utils\TextFormat;
use pocketmine\Server;

class CoreCommand extends PluginCommand {

    private $plugin;

    public function __construct(Core $plugin) {
        parent::__construct("core", $plugin);
        $this->setDescription("nothing");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
    	if($sender->hasPermission("core.jail")){
            Core::$form->SettingsForm($sender);
             return true;
        }
    }
}