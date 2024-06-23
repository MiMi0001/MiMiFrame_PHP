<?php

namespace Mimi\engine;

class MimiDB
{
    private static bool $instanceExist = false;
    private static MimiDB $instance;
    private \PDO $pdo;

    public function __construct() {
        $mimi = MimiApp::getInstance();

        $host = $mimi->appConfig["database"]["host"];
        $dbname = $mimi->appConfig["database"]["dbname"];
        $user = $mimi->appConfig["database"]["user"];
        $password = $mimi->appConfig["database"]["password"];

        $dsn = "mysql:host=" . $host . ";dbname=" . $dbname;
        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function getInstance(): ?MimiDB {
        if (self::$instanceExist) return self::$instance;
        else {
            self::$instance = new MimiDB();
            self::$instanceExist = true;
            return self::$instance;
        }
    }    

    /**
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }
}