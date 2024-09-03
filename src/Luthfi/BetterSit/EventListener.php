<?php

namespace Luthfi\BetterSit;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\player\Player;
use Luthfi\BetterSit\Main;

class EventListener implements Listener {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();

        if (!$this->plugin->isSitEnabled($player->getName())) {
            return;
        }

        if ($block instanceof Slab || $block instanceof Stair) {
            $event->cancel();
            $this->sitOnBlock($player, $block);
        } elseif ($event->getTargetEntity() instanceof Player) {
            $target = $event->getTargetEntity();
            if ($player !== $target) {
                $event->cancel();
                $this->sitOnPlayer($player, $target);
            }
        }
    }

    private function sitOnBlock(Player $player, $block): void {
        $pos = $block->getPosition()->add(0.5, 1.5, 0.5);
        $player->teleport(new Vector3($pos->x, $pos->y, $pos->z));
    }

    private function sitOnPlayer(Player $player, Player $target): void {
        $pos = $target->getPosition()->add(0, 2, 0);
        $player->teleport(new Vector3($pos->x, $pos->y, $pos->z));
        $player->sendMessage("You are now sitting on " . $target->getName() . " head!");
    }
}
