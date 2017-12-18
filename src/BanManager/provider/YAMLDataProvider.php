<?php

namespace BanManager\provider;

use BanManager\utils\Ban;
use pocketmine\Player;
use pocketmine\utils\Config;

class YAMLDataProvider implements DataProvider{
    private $dataFolder;

    private $banList = [];
    private $banIpList = [];
    private $muteList = [];

    public function __construct($dataFolder){
        $this->dataFolder = $dataFolder;
        foreach(["", "PlayerBans", "IPBans", "MuteBans", "XuidData", "PlayerData", "IPData"] as $folder){
            if(!is_dir($this->dataFolder . $folder)){
                @mkdir($this->dataFolder . $folder);
            }
        }

        // TODO: Load banlists
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
        @mkdir($this->dataFolder . "PlayerBans/" . $folder = substr($xuid, 0, 2));
        $data = new Config($this->dataFolder . "PlayerBans/" . $folder . "/$xuid.yml", Config::YAML);
        $data->set("expireTime", $time ? $time + time() : 0);
        $data->set("reason", $reason);
        $data->save();
    }

    public function banIP(string $ipAddress, int $time = 0, string $reason = null){
        @mkdir($this->dataFolder . "IPBans/" . $folder = explode(".", $ipAddress)[0]);
        $data = new Config($this->dataFolder . "IBans/" . $folder . "/$ipAddress.yml", Config::YAML);
        $data->set("expireTime", $time ? $time + time() : 0);
        $data->set("reason", $reason);
        $data->save();
    }

    public function unbanPlayer(string $xuid){
        @unlink($this->dataFolder . "PlayerBans/" . substr($xuid, 0, 2) . "/$xuid.yml");
    }

    public function unbanIP(string $ipAddress){
        @unlink($this->dataFolder . "IBans/" . explode(".", $ipAddress)[0] . "/$ipAddress.yml");
    }

    public function getPlayerBan(string $xuid){
        @mkdir($this->dataFolder . "PlayerBans/" . $folder = substr($xuid, 0, 2));
        if(file_exists($this->dataFolder . "PlayerBans/" . $folder . "/$xuid.yml")){
            $data = new Config($this->dataFolder . "PlayerBans/" . $folder . "/$xuid.yml", Config::YAML);
            return new Ban(Ban::BAN_TYPE_PLAYER, $xuid, $data->get("expireTime"), $data->get("reason"));
        }
        return null;
    }

    public function getIPBan(string $ipAddress){
        @mkdir($this->dataFolder . "IPBans/" . $folder = explode(".", $ipAddress)[0]);
        if(file_exists($this->dataFolder . "IBans/" . $folder . "/$ipAddress.yml")){
            $data = new Config($this->dataFolder . "IBans/" . $folder . "/$ipAddress.yml", Config::YAML);
            return new Ban(Ban::BAN_TYPE_IP_ADDRESS, $ipAddress, $data->get("expireTime"), $data->get("reason"));
        }
        return null;
    }

    public function verifyPlayerLogin(Player $player){
        return $this->getPlayerBan($player->getXuid()) ?? $this->getIPBan($player->getAddress());
    }

    public function mutePlayer(string $xuid, int $time = 0, string $reason = null){
        @mkdir($this->dataFolder . "MuterBans/" . $folder = substr($xuid, 0, 2));
        $data = new Config($this->dataFolder . "MuteBans/" . $folder . "/$xuid.yml", Config::YAML);
        $data->set("expireTime", $time ? $time + time() : 0);
        $data->set("reason", $reason);
        $data->save();
    }

    public function unmutePlayer(string $xuid){
        @unlink($this->dataFolder . "MuteBans/" . substr($xuid, 0, 2) . "/$xuid.yml");
    }

    public function getPlayerMuteBan(string $xuid){
        @mkdir($this->dataFolder . "MuteBans/" . $folder = substr($xuid, 0, 2));
        if(file_exists($this->dataFolder . "MuteBans/" . $folder . "/$xuid.yml")){
            $data = new Config($this->dataFolder . "MuteBans/" . $folder . "/$xuid.yml", Config::YAML);
            return new Ban(Ban::BAN_TYPE_PLAYER, $xuid, $data->get("expireTime"), $data->get("reason"));
        }
        return null;
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

    public function getBanList(): array{
        return $this->banList;
    }

    public function getBanIpList(): array{
        return $this->banIpList;
    }

    public function getMuteList(): array{
        return $this->muteList;
    }
}