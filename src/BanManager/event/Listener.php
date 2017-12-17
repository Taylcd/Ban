<?php

namespace BanManager\event;

use BanManager\BanManager;
use BanManager\utils\Ban;
use pocketmine\event\player\PlayerPreLoginEvent;

class Listener implements \pocketmine\event\Listener{
    /** @var BanManager */
    private $plugin;

    public function __construct(BanManager $plugin){
        $this->plugin = $plugin;
    }

    public function onPlayerPreLogin(PlayerPreLoginEvent $event){
        /** @var Ban $ban */
        if(($ban = $this->plugin->getDataProvider()->verifyPlayerLogin($event->getPlayer())) !== null && !$ban->isExpired()){
            if($ban->getType() == Ban::BAN_TYPE_PLAYER){
                $message = $this->plugin->getMessage("banned.player");
            } else {
                $message = $this->plugin->getMessage("banned.ipAddress");
            }
            if($ban->getExpireTime() === 0){
                $message .= "\n" . $this->plugin->getMessage("banned.noExpire");
            } else {
                $message .= "\n" . $this->plugin->getMessage("banned.expireTime", date("Y/m/d", $ban->getExpireTime()), date("h:i", $ban->getExpireTime()));
            }
            if($ban->getReason() !== null){
                $message .= "\n" . $this->plugin->getMessage("banned.reason", $ban->getReason());
            }
            $event->setKickMessage($message);
            $event->setCancelled(true);
        } else {
            $this->plugin->getDataProvider()->processPlayerLogin($event->getPlayer());
        }
    }
}