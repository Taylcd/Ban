<?php

namespace BanManager\event;

use BanManager\utils\Ban;
use pocketmine\event\Cancellable;
use pocketmine\event\Event;

abstract class BannedEvent extends Event implements Cancellable{
    public static $handlerList = null;

    /** @var Ban */
    private $ban;

    public function __construct(Ban $ban){
        $this->ban = $ban;
    }

    public function getBan() : Ban{
        return $this->ban;
    }
}