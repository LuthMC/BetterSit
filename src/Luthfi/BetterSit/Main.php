<?php

namespace Luthfi\BetterSit;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use Luthfi\BetterSit\SitToggleCommand;
use Luthfi\BetterSit\EventListener;

class Main extends PluginBase implements Listener {

    private static $instance;
    private $sitEnabled = [];

    public static function getInstance(): Main {
        return self::$instance;
    }

    public function onEnable(): void {
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register("sittoggle", new SitToggleCommand($this));
    }

    public function isSitEnabled(string $playerName): bool {
        return $this->sitEnabled[$playerName] ?? true;
    }

    public function toggleSit(string $playerName): void {
        $this->sitEnabled[$playerName] = !$this->sitEnabled[$playerName];
    }
}
