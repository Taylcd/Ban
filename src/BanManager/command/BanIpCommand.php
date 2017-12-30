<?php

namespace BanManager\command;

use BanManager\BanManager;
use BanManager\utils\Time;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;

class BanIpCommand extends Command{
    /** @var BanManager */
    private $plugin;

    public function __construct(BanManager $plugin){
        parent::__construct(
            $plugin->getConfig()->getNested("commands.ban-ip"),
            $plugin->getMessage("description.banIp"),
            $plugin->getMessage("usage.banIp")
        );
        $this->setPermission("banmanager.command.ban.ip");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$this->testPermission($sender)){
            return true;
        }

        if(count($args) === 0){
            throw new InvalidCommandSyntaxException();
        }

        $ip = array_shift($args);
        $time = array_shift($args) ?? 0;
        $reason = implode(" ", $args);
        if((filter_var($ip, FILTER_VALIDATE_IP) === false)){
            $sender->sendMessage($this->plugin->getMessage("command.ipNotValid"));
        } else {
            $this->plugin->getDataProvider()->banIP($ip, $time = (strtotime(Time::format($time)) - time()), $reason);
            $sender->sendMessage($this->plugin->getMessage("command.ipBanned", $ip, $time > 0 ? date("Y/m/d h:i", $time + time()) . $this->plugin->getMessage("command.expireInSeconds", $time) : $this->plugin->getMessage("command.permanent"), trim($reason) ? $reason : $this->plugin->getMessage("command.none")));
        }
        return true;
    }
}