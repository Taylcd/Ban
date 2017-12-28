<?php

namespace BanManager\event;

use BanManager\BanManager;
use BanManager\utils\Ban;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerLoginEvent;

class Listener implements \pocketmine\event\Listener{
    /** @var BanManager */
    private $plugin;

    public function __construct(BanManager $plugin){
        $this->plugin = $plugin;
    }

    public function onPlayerLogin(PlayerLoginEvent $event){
        /** @var Ban $ban */
        if(($ban = $this->plugin->getDataProvider()->verifyPlayerLogin($event->getPlayer())) !== null && !$ban->isExpired()){
            $this->plugin->getServer()->getPluginManager()->callEvent($ev = new PlayerLoginFailedEvent($event->getPlayer(), $ban));
            if(!$ev->isCancelled()){
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
                if($ban->getReason() != null){
                    $message .= "\n" . $this->plugin->getMessage("banned.reason", $ban->getReason());
                }
                $event->setKickMessage($message);
                $event->setCancelled(true);
                return;
            }
        }
        $this->plugin->getDataProvider()->processPlayerLogin($event->getPlayer());
    }

    public function onPlayerChat(PlayerChatEvent $event){
        /** @var Ban $ban */
        if(($ban = $this->plugin->getDataProvider()->getPlayerMuteBan($event->getPlayer()->getXuid())) !== null && !$ban->isExpired()){
            $message = $this->plugin->getMessage("");
            if($ban->getExpireTime() === 0){
                $message .= "\n" . $this->plugin->getMessage("muted.noExpire");
            } else {
                $message .= "\n" . $this->plugin->getMessage("muted.expireTime", date("Y/m/d", $ban->getExpireTime()), date("h:i", $ban->getExpireTime()));
            }
            if($ban->getReason() != null){
                $message .= "\n" . $this->plugin->getMessage("muted.reason", $ban->getReason());
            }
            $event->setCancelled(true);
            return;
        }
        $this->plugin->getDataProvider()->processPlayerLogin($event->getPlayer());
    }
}