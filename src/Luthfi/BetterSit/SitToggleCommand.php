<?php

namespace Luthfi\BetterSit;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Luthfi\BetterSit\Main;

class SitToggleCommand extends Command {

    private $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("sittoggle", "Toggle sit mode", "/sittoggle", []);
        $this->plugin = $plugin;
        $this->setPermission("bettersit.sittoggle");
    }

    public function execute(CommandSender $sender, string $label, array $args): void {
        if ($sender instanceof Player) {
            $this->plugin->toggleSit($sender->getName());
            $status = $this->plugin->isSitEnabled($sender->getName()) ? "enabled" : "disabled";
            $sender->sendMessage("Sitting is now " . $status);
        } else {
            $sender->sendMessage("This command can only be used by players.");
        }
    }
}
