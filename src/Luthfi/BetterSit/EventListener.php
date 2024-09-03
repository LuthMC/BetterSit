<?php

namespace Luthfi\BetterSit;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
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
        } else {
            foreach ($player->getWorld()->getPlayers() as $target) {
                if ($player !== $target && $this->calculateDistance($player, $target) <= 2) {
                    $event->cancel();
                    $this->sitOnPlayer($player, $target);
                    break;
                }
            }
        }
    }

    private function sitOnBlock(Player $player, $block): void {
        $pos = $block->getPosition()->add(0.5, 1.5, 0.5);
        $player->teleport(new Vector3($pos->x, $pos->y, $pos->z));
    }

    private function sitOnPlayer(Player $player, Player $target): void {
        $pos = $target->getPosition()->add(0, 2, 0);
        $player->teleport($pos);
        $player->sendMessage("You are now sitting on " . $target->getName() . "'s head!");

        $this->plugin->getScheduler()->scheduleRepeatingTask(new class($player, $target) extends Task {
            private $sittingPlayer;
            private $targetPlayer;

            public function __construct(Player $sittingPlayer, Player $targetPlayer) {
                $this->sittingPlayer = $sittingPlayer;
                $this->targetPlayer = $targetPlayer;
            }

            public function onRun(): void {
                if (!$this->targetPlayer->isOnline() || !$this->sittingPlayer->isOnline()) {
                    $this->getHandler()->cancel();
                    return;
                }

                $pos = $this->targetPlayer->getPosition()->add(0, 2.0, 0);
                $this->sittingPlayer->teleport($pos);
            }
        }, 1);
    }

    private function calculateDistance(Player $player1, Player $player2): float {
        $pos1 = $player1->getPosition();
        $pos2 = $player2->getPosition();
        return $pos1->distance($pos2);
    }
}
