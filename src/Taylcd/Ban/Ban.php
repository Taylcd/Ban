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
        while(($resource = $this->getResource("lang/" . ($lang = $this->getConfig()->get("lang", "eng")) . ".yml")) === null){
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
        $commands = $this->getConfig()->get("commands");
        if($this->getConfig()->get("override-default-commands", true)){
            foreach($commands as $name){
                if(($command = $this->getServer()->getCommandMap()->getCommand($name)) !== null){
                    $command->unregister($this->getServer()->getCommandMap());
                }
            }
        }
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