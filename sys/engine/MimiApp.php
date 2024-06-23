<?php
namespace Mimi\engine;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;

use Mimi\engine\router\Router;
use Mimi\engine\router\Request;
use Mimi\engine\utils\MimiLogger;
use Mimi\model\MimiDB;
use Mimi\model\QueryRepository;


/**
 * Mimi 2024.04.21.
 * Singleton class.
 * Main class of the project. Centralized components available from here
 * 
 * Properties:
 * $router: URL routing
 * $config: stores the configuration of the app. Associative array, generated from the config.json file
 * $request: methods for handling HTTP request 
 * $logger: can be used to log messages
 * $pdo: pdo for the database
 * $queryRepository: repository of SQL queries
 * webDir: path of the public web folder
 */
class MimiApp {
    private static bool $instanceExist = false;
    private static MimiApp $instance;
    public Router $router;
    public Config $config;
    public Request $request;
    public MimiLogger $logs;
    public \PDO $pdo;
    public QueryRepository $queryRepository;
    private string $cfgDir;
    private string $webDir;
    private string $logsDir;

    private function __construct() {
            set_exception_handler([MimiApp::class, "exception_handler"]);
            $this->webDir = dirname(__DIR__, 2)."/web";
            $this->cfgDir = dirname(__DIR__, 1)."/cfg";
            $this->logsDir = dirname(__DIR__, 1)."/logs";
            $this->request = Request::getInstance();            
            $this->config = Config::getInstance($this->cfgDir);
            $this->logs = MimiLogger::getInstance($this->logsDir, $this->config);
            $this->router = Router::getInstance();    
            $this->pdo = MimiDB::getInstance($this->config)->getPdo();        

            // $this->queryRepository = QueryRepository::getInstance();
    
            // $views = dirname(__DIR__, 1) . '/views';
            // $cache = dirname(__DIR__, 1) . '/cache';
    }

    public static function getInstance(): ?MimiApp {
        if ( !self::$instanceExist ) {
            self::$instanceExist = true;
            self::$instance = new MimiApp();
            return self::$instance;
        }
        else return self::$instance;
    }

    static function exception_handler($e) {
        $logger = new Logger('uncaught');
        $stream_handler = new StreamHandler(dirname(__DIR__, 1)."/logs/errors.json");
        $stream_handler->setFormatter(new JsonFormatter(JsonFormatter::BATCH_MODE_JSON, true));
        $logger->pushHandler($stream_handler);
        $logger->error("Uncaught exception", array('exception' => $e));
        exit(1);
    }
  
    public function getCfgDir(): string {
        return $this->cfgDir;
    }    

    public function getWebDir():string {
        return $this->webDir;                
    }    

    public function getLogsDir() {
        return $this->logsDir;
    }

    public function run() {
        $this->router->resolve();
    }
}