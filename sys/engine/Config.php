<?php

namespace Mimi\engine;

class Config
{
    private static bool $instanceExist = false;
    private static Config $instance;

    const CONFIG_FILE_NAME = "config.json";
    private string $cfgDir;

    private function __construct($cfgDir)    
    {
        $this->cfgDir = $cfgDir;
    }

    public static function getInstance($cfgDir): Config {
        if (self::$instanceExist) return self::$instance;
        else {
            self::$instance = new Config($cfgDir);
            self::$instanceExist = true;
            return self::$instance;
        }
    }

    public function getConfig() {
        $jsonString = file_get_contents($this->cfgDir."/".self::CONFIG_FILE_NAME);
        return json_decode($jsonString, true);
    }

    public static function getConfigStat() {
        $baseDir = dirname(__DIR__, 1);
        $jsonString = file_get_contents("$baseDir/".self::CONFIG_FILE_NAME);
        return json_decode($jsonString, true);
    }
}