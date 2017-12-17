<?php

namespace BanManager\utils;

class Time{
    const COUNT = 11;
    const SHORT = ["second", "sec", "s", "month", "min", "m", "hr", "h", "d", "yr", "y"];
    const REPLACE = ["second", "second", "second", "month", "minute", "minute", "hour", "hour", "day", "year", "year"];

    public static function format(string $time){
        for($i = 0; $i < self::COUNT; $i ++){
            if(strpos($time, self::SHORT[$i]) !== false){
                return str_replace(self::SHORT[$i], self::REPLACE[$i], $time);
            }
        }
        return $time . "day";
    }
}