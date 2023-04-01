<?php
use Noodlehaus\Config;

class AppConfig
{
    private static ?Config $configObj = null;
    public static function get(string $key)
    {
        if (!self::$configObj)
            self::createInstance();

        return self::$configObj->get($key);
    }

    private static function createInstance() : void
    {
        self::$configObj = Config::load([
            __DIR__ . "/../config/config.json",
            //__DIR__ . "/../config/config_local.json"
        ]);
    }

}