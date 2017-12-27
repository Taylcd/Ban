<?php

namespace BanManager\event;

use BanManager\utils\Ban;

class IpBannedEvent extends BannedEvent{
    public function __construct(Ban $ban){
        parent::__construct($ban);
    }
}