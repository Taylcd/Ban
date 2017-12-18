<?php

namespace BanManager\command;

use BanManager\BanManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;

class UnbanCommand extends Command{
    /** @var BanManager */
    private $plugin;

    public function __construct(BanManager $plugin){
        parent::__construct(
            $plugin->getConfig()->getNested("commands.unban"),
            $plugin->getMessage("description.unban"),
            $plugin->getMessage("usage.unban")
        );
        $this->setPermission("banmanager.command.unban.player");
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
        if(!($xuid = $this->plugin->getDataProvider()->getLastVerifiedXuid($name))){
            $sender->sendMessage($this->plugin->getMessage("command.playerNotFound", $name));
        } else {
            $this->plugin->getDataProvider()->unbanPlayer($xuid);
            $sender->sendMessage($this->plugin->getMessage("command.playerUnbanned", $name, $xuid));
        }
        return true;
    }
}