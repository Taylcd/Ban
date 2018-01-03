<?php

namespace BanManager;

use BanManager\command\BanCommand;
use BanManager\command\BanIpCommand;
use BanManager\command\MuteCommand;
use BanManager\command\UnbanCommand;
use BanManager\command\UnbanIpCommand;
use BanManager\command\UnmuteCommand;
use BanManager\event\Listener;
use BanManager\provider\DataProvider;
use BanManager\provider\MySQLDataProvider;
use BanManager\provider\YAMLDataProvider;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginDescription;
use pocketmine\utils\Config;
use BanManager\exception\MessageNotFoundException;

class BanManager extends PluginBase{
    const CONFIG_VERSION = 1;

    /** @var Config */
    private $lang;

    /** @var BanManager */
    private static $instance;

    /** @var DataProvider */
    private $dataProvider;

    public function onLoad(){
        self::$instance = $this;

        $this->saveDefaultConfig();
        while(($resource = $this->getResource("lang/" . ($lang = $this->getConfig()->get("lang", "eng")) . ".yml")) === null){
            $this->getLogger()->warning("Language resource $lang not found, using eng as default.");
            $this->getConfig()->set("lang", "eng");
        }
        if(!file_exists($out = $this->getDataFolder() . "message.yml")){
            stream_copy_to_stream($resource, $fp = fopen($out, "wb"));
            fclose($fp);
            fclose($resource);
        }
        $this->lang = new Config($this->getDataFolder() . 'message.yml', Config::YAML);

        if($this->getConfig()->get('config-version') < self::CONFIG_VERSION){
            rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config.old.yml");
            $this->saveDefaultConfig();
            $this->getConfig()->reload();


            $this->getLogger()->notice($this->getMessage("console.configOutdated"));
        }

        if($this->getConfig()->get("check-update", true)){
            $this->getLogger()->info($this->getMessage("update.checking"));
            try{
                if(($version = (new PluginDescription(file_get_contents("https://raw.githubusercontent.com/Taylcd/BanManager/master/plugin.yml")))->getVersion()) != $this->getDescription()->getVersion()){
                    $this->getLogger()->notice($this->getMessage("update.newVersion", $version, $this->getDescription()->getWebsite()));
                } else {
                    $this->getLogger()->info($this->getMessage("update.upToDate"));
                }
            } catch(\Exception $ex) {
                $this->getLogger()->warning($this->getMessage("update.failed"));
            }
        }
    }

    public function onEnable(){
        $commands = $this->getConfig()->get("commands");
        if($this->getConfig()->get("override-default-commands", true)){
            foreach($commands as $name){
                if(($command = $this->getServer()->getCommandMap()->getCommand($name)) !== null){
                    $command->setLabel("");
                    $command->unregister($this->getServer()->getCommandMap());
                }
            }
        }
        $this->getServer()->getCommandMap()->registerAll("", [
            new BanCommand($this),
            new BanIpCommand($this),
            new UnbanCommand($this),
            new UnbanIpCommand($this),
            new MuteCommand($this),
            new UnmuteCommand($this)
        ]);

        switch(strtolower($provider = $this->getConfig()->get("database-provider", "YAML"))){
            default:
                $this->getLogger()->notice($this->getMessage("console.providerNotSupported", $provider));
            case "yaml":
                $this->dataProvider = new YAMLDataProvider($this->getDataFolder() . "data/");
                break;
            // TODO: Implement MySQL data provider
        }
        $this->getServer()->getPluginManager()->registerEvents(new Listener($this), $this);
    }

    public function getMessage($key, ...$replacement) : string{
        if(!$message = $this->lang->getNested($key)){
            if($message = (new Config($this->getFile() . "resources/lang/" . $this->getConfig()->get("lang", "eng") . ".yml", Config::YAML))->getNested($key)){
                $this->lang->setNested($key, $message);
                $this->lang->save();
            } else {
                $this->getLogger()->info($message);
                throw new MessageNotFoundException("Message $key not found.");
            }
        }
        foreach($replacement as $index => $value){
            $message = str_replace("%$index", $value, $message);
        }
        return $message;
    }

    public function getDataProvider(){
        return $this->dataProvider;
    }

    public static function getInstance() : BanManager{
        return self::$instance;
    }
}