<?php

namespace BanManager\command;

use BanManager\BanManager;
use BanManager\utils\Time;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;

class BanCommand extends Command{
    /** @var BanManager */
    private $plugin;

    public function __construct(BanManager $plugin){
        parent::__construct(
            $plugin->getConfig()->getNested("commands.ban"),
            $plugin->getMessage("description.ban"),
            $plugin->getMessage("usage.ban")
        );
        $this->setPermission("banmanager.command.ban.player");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$this->testPermission($sender)){
            return true;
        }

        if(count($args) === 0){
            throw new InvalidCommandSyntaxException();
        }

        $name = array_shift($args);
        $time = array_shift($args) ?? 0;
        $reason = implode(" ", $args);
        if(!($xuid = $this->plugin->getDataProvider()->getLastVerifiedXuid($name))){
            $sender->sendMessage($this->plugin->getMessage("ban.playerNotFound", $name));
        } else {
            $this->plugin->getDataProvider()->banPlayer($xuid, $time = (strtotime(Time::format($time)) - time()), $reason);
            $sender->sendMessage($this->plugin->getMessage("ban.banned", $name, $xuid, $time > 0 ? date("Y/m/d h:i", $time + time()) . $this->plugin->getMessage("ban.expireInSeconds", $time) : $this->plugin->getMessage("ban.permanent-ban"), trim($reason) ? $reason : $this->plugin->getMessage("ban.noneReason")));
        }
        return true;
    }
}