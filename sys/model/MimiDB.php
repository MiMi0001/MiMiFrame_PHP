<?php

namespace Mimi\model;

use Mimi\MimiApp;

// Singleton class
class MimiDB
{
    private static MimiDB $instance;
    private static bool $isInstanceExists = false;
    private \PDO $pdo;

    private function __construct($config) {
        $dbConfig = $config->getConfig()["database"];


        $host=$dbConfig["host"];
        $database=$dbConfig["database"];
        $username=$dbConfig["username"];
        $password=$dbConfig["password"];
        $dsn="mysql:host=$host;dbname=$database";

        $this->pdo=new \PDO($dsn, $username, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance($config): MimiDB{
        if (!self::$isInstanceExists) {
            self::$instance = new MimiDB($config);
            self::$isInstanceExists = true;
        }

        return self::$instance;
    }

    public function getPdo(): \PDO {
        return $this->pdo;
    }
}