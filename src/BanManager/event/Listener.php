<?php

namespace BanManager\event;

use BanManager\BanManager;
use pocketmine\event\player\PlayerLoginEvent;

class Listener implements \pocketmine\event\Listener{
    /** @var BanManager */
    private $plugin;

    public function __construct(BanManager $plugin){
        $this->plugin = $plugin;
    }

    public function onPlayerLogin(PlayerLoginEvent $event){
        $this->plugin->getDataProvider()->processPlayerLogin($event->getPlayer());
    }
}