<?php

namespace Mimi\engine\utils;

class MimiLogger
{
    private static bool $instanceExist = false;
    private static MimiLogger $instance;
    private array $logs;

    private function __construct($logsDir, $config)
    {         
        $this->logs = $config->getConfig()["logs"];

        foreach ($this->logs as $log=>$logOptions) $this->logs[$log]["file"] = fopen("$logsDir/".$logOptions["fileName"], 'a');
    }

    function __destruct(){
        foreach ($this->logs as $log=>$logFileName) {
            fclose($this->logs[$log]["file"]);
        }        
    }

    public function getInstance($logsDir, $config): MimiLogger {
        if (self::$instanceExist) return self::$instance;
        else {
            self::$instance = new MimiLogger($logsDir, $config);
            self::$instanceExist = true;
            return self::$instance;
        }
    }

    public function writeMessage(string $log, string $message, string $source="") {
        $timestamp = date('Y/m/d H:i:s', time());
        if (!empty($source)) $logMessage = "<Timestamp: $timestamp> <Source: $source> <Log: $message>\n";
        else $logMessage = "<Timestamp: $timestamp> <Log: $message>\n";
        switch ($this->logs[$log]["handler"]) {
            case "MimiLogger":
                if ($this->logs[$log]["enabled"]===true) fwrite($this->logs[$log]["file"], $logMessage, strlen($logMessage));
                break;
            case "Monolog":
                // To be implemented here
                break;
        }       
    }
}