<?php

namespace BanManager\provider;

use BanManager\utils\Ban;
use pocketmine\Player;
use pocketmine\utils\Config;

class YAMLDataProvider implements DataProvider{
    private $dataFolder;

    public function __construct($dataFolder){
        $this->dataFolder = $dataFolder;
        foreach(["", "PlayerBans", "IPBans", "MuteBans", "XuidData", "PlayerData", "IPData"] as $folder){
            if(!is_dir($this->dataFolder . $folder)){
                @mkdir($this->dataFolder . $folder);
            }
        }
    }

    public function processPlayerLogin(Player $player){
        $name = trim(strtolower($player->getName()));

        @mkdir($this->dataFolder . "XuidData/" . $name{0});
        $data = new Config($this->dataFolder . "XuidData/" . $name{0} . "/$name.yml", Config::YAML);
        $data->set("lastVerifiedXuid", $xuid = $player->getXuid());
        $data->save();

        @mkdir($this->dataFolder . "PlayerData/" . $folder = substr($xuid, 0, 2));
        $data = new Config($this->dataFolder . "PlayerData/" . $folder . "/$xuid.yml", Config::YAML);
        $usedIP = $data->get("usedIP", []);
        if(!in_array($address = $player->getAddress(), $usedIP)){
            array_push($usedIP, $address);
        }
        $data->set("usedIP", $usedIP);
        $data->set("lastLoginTime", time());
        $data->save();

        @mkdir($this->dataFolder . "IPData/" . $folder = explode(".", $address)[0]);
        $data = new Config($this->dataFolder . "IPData/" . $folder . "/$address.yml", Config::YAML);
        $usedAccount = $data->get("usedAccount", []);
        if(!in_array($xuid, $usedAccount)){
            array_push($usedAccount, $xuid);
        }
        $data->set("usedAccount", $usedAccount);
        $data->set("lastLoginTime", time());
        $data->save();
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

    public function getPlayerBan(string $xuid) : Ban{
        // TODO: Implement isPlayerBanned() method.
    }

    public function getIPBan(string $ipAddress) : Ban{
        // TODO: Implement isIPBanned() method.
    }

    public function verifyPlayerLogin(Player $player){
        // TODO: Implement verifyPlayerLogin() method.
    }

    public function mutePlayer(string $xuid, int $time = 0, string $reason = null){
        // TODO: Implement mutePlayer() method.
    }

    public function unmutePlayer(string $xuid){
        // TODO: Implement unmutePlayer() method.
    }

    public function getPlayerMuteBan(string $xuid) : Ban{
        // TODO: Implement isPlayerMuted() method.
    }

    public function blockPlayer(string $xuid, int $time = 0, string $reason = null) : int{
        // TODO: Implement blockPlayer() method.
    }

    public function blockIP(string $ipAddress, int $time = 0, string $reason = null) : int{
        // TODO: Implement blockIP() method.
    }

    public function close(){

    }

    public function getLastVerifiedXuid(string $name){
        $name = trim(strtolower($name));

        @mkdir($this->dataFolder . "XuidData/" . $name{0});
        $data = new Config($this->dataFolder . "XuidData/" . $name{0} . "/$name.yml", Config::YAML);
        return $data->get("lastVerifiedXuid");
    }
}