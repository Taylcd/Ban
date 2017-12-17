<?php

namespace BanManager\provider;

use pocketmine\Player;

class MySQLDataProvider implements DataProvider{
    public function __construct(){
        // TODO: Implement __construct() method.
    }

    public function processPlayerLogin(Player $player){
        // TODO: Implement processPlayerLogin() method.
    }

    public function banPlayer(string $xuid, int $time = 0, string $reason = null){
        // TODO: Implement banPlayer() method.
    }

    public function banIP(string $ipAddress, int $time = 0, string $reason = null){
        // TODO: Implement banIP() method.
    }

    public function unbanPlayer(string $xuid){
        // TODO: Implement unbanPlayer() method.
    }

    public function unbanIP(string $ipAddress){
        // TODO: Implement unbanIP() method.
    }

    public function isPlayerBanned(string $xuid): bool{
        // TODO: Implement isPlayerBanned() method.
    }

    public function isIPBanned(string $ipAddress): bool{
        // TODO: Implement isIPBanned() method.
    }

    public function mutePlayer(string $xuid, int $time = 0, string $reason = null){
        // TODO: Implement mutePlayer() method.
    }

    public function unmutePlayer(string $xuid){
        // TODO: Implement unmutePlayer() method.
    }

    public function isPlayerMuted(string $xuid): bool{
        // TODO: Implement isPlayerMuted() method.
    }

    public function blockPlayer(string $xuid, int $time = 0, string $reason = null): int{
        // TODO: Implement blockPlayer() method.
    }

    public function blockIP(string $ipAddress, int $time = 0, string $reason = null): int{
        // TODO: Implement blockIP() method.
    }

    public function close(){
        // TODO: Implement close() method.
    }
}