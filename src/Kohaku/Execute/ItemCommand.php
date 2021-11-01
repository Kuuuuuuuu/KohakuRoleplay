<?php

namespace Kohaku\Execute;

use Kohaku\Core;
use pocketmine\command\{CommandSender, PluginCommand};
use pocketmine\utils\TextFormat;
use pocketmine\Server;

class ItemCommand extends PluginCommand {

    private $plugin;

    public function __construct(Core $plugin) {
        parent::__construct("item", $plugin);
        $this->setDescription("nothing");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        Core::$utils->getItem($sender);
        return true;
    }
}