<?php

namespace BanManager\provider;

use pocketmine\Player;

interface DataProvider{
    public function processPlayerLogin(Player $player);

    public function banPlayer(string $xuid, int $time = 0, string $reason = null);

    public function banIP(string $ipAddress, int $time = 0, string $reason = null);

    public function unbanPlayer(string $xuid);

    public function unbanIP(string $ipAddress);

    public function isPlayerBanned(string $xuid) : bool;

    public function isIPBanned(string $ipAddress) : bool;

    public function mutePlayer(string $xuid, int $time = 0, string $reason = null);

    public function unmutePlayer(string $xuid);

    public function isPlayerMuted(string $xuid) : bool;

    public function blockPlayer(string $xuid, int $time = 0, string $reason = null) : int;

    public function blockIP(string $ipAddress, int $time = 0, string $reason = null) : int;

    public function close();
}