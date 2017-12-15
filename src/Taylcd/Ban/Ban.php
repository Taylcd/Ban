<?php

namespace Taylcd\Ban;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Ban extends PluginBase{
    const CONFIG_VERSION = 1;

    /** @var Config */
    private $lang;

    public function onLoad(){
        $this->saveDefaultConfig();
        if(($resource = $this->getResource($lang = $this->getConfig()->get("lang", "eng"))) === null){
            $this->getLogger()->warning("Language resource $lang not found, using eng as default.");
            $this->getConfig()->set("lang", "eng");
            $this->getConfig()->save();
        }
        $out = $this->getDataFolder() . "message.yml";
        if(!file_exists($out)){
            stream_copy_to_stream($resource, $fp = fopen($out, "wb"));
            fclose($fp);
            fclose($resource);
        }
        $this->lang = new Config($this->getDataFolder() . 'message.yml', Config::YAML);

        if($this->getConfig()->get('config-version') < self::CONFIG_VERSION){
            rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config.old.yml");
            $this->saveDefaultConfig();
            $this->getConfig()->reload();
            $this->getLogger()->notice($this->getMessage("console.config-outdated"));
        }
    }

    public function onEnable(){
        if($this->getConfig()->get("override-default-commands", true)){
            $this->getCommand("ban")->unregister($this->getServer()->getCommandMap());
            $this->getCommand("ban-ip")->unregister($this->getServer()->getCommandMap());
            $this->getCommand("pardon")->unregister($this->getServer()->getCommandMap());
            $this->getCommand("pardon-ip")->unregister($this->getServer()->getCommandMap());
        }
        $commands = $this->getConfig()->get("commands");
        // TODO: register commands
    }

    public function getMessage($key, ...$replacement) : string{
        $message = $this->lang->getNested($key, 'Missing message: ' . $key);
        foreach($replacement as $index => $value){
            $message = str_replace("%$index", $value, $message);
        }
        return $message;
    }
}