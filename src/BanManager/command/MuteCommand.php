<?php

namespace BanManager\command;

use BanManager\BanManager;
use BanManager\utils\Time;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;

class MuteCommand extends Command{
    /** @var BanManager */
    private $plugin;

    public function __construct(BanManager $plugin){
        parent::__construct(
            $plugin->getConfig()->getNested("commands.mute"),
            $plugin->getMessage("description.mute"),
            $plugin->getMessage("usage.mute")
        );
        $this->setPermission("banmanager.command.mute");
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
            $sender->sendMessage($this->plugin->getMessage("command.playerNotFound", $name));
        } else {
            $this->plugin->getDataProvider()->mutePlayer($xuid, $time = (strtotime(Time::format($time)) - time()), $reason);
            $sender->sendMessage($this->plugin->getMessage("command.playerMuted", $name, $xuid, $time > 0 ? date("Y/m/d h:i", $time + time()) . $this->plugin->getMessage("command.expireInSeconds", $time) : $this->plugin->getMessage("command.permanent"), trim($reason) ? $reason : $this->plugin->getMessage("command.none")));
        }
        return true;
    }
}