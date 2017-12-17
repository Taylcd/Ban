<?php

namespace BanManager\utils;

class Ban{
    public const BAN_TYPE_PLAYER = 0;
    public const BAN_TYPE_IP_ADDRESS = 1;
    public const BAN_TYPE_MUTE = 2;

    private $type;
    private $banned;
    private $expireTime;
    private $reason;

    public function __construct(int $type, string $banned, int $expireTime, string $reason = null){
        $this->type = $type;
        $this->banned = $banned;
        $this->expireTime = $expireTime;
        $this->reason = $reason;
    }

    public function getType() : int{
        return $this->type;
    }

    public function getBanned() : string{
        return $this->banned;
    }

    public function getExpireTime() : int{
        return $this->expireTime;
    }

    public function expireIn() : int{
        return $this->expireTime - time();
    }

    public function isExpired() : bool{
        return ($this->expireTime - time()) <= 0;
    }

    public function getReason(){
        return $this->reason;
    }
}