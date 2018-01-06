<?php

namespace BanManager\event;

use BanManager\utils\Ban;

class PlayerMutedEvent extends BannedEvent{
    public function __construct(Ban $ban){
        parent::__construct($ban);
    }
}