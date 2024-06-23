<?php

namespace Mimi\engine\router;

use Mimi\engine\MimiApp;
use Mimi\model\JwtTokenCrm;
use Mimi\model\MimiSystem;
use Mimi\engine\utils\MimiJWT;
use Mimi\view\ApiEndpoint;

class Router
{
    private static bool $instanceExist = false;
    private static Router $instance;
    protected array $routes = [];

    private function __construct() {        
    }

    public function getInstance(): ?Router {
        if (self::$instanceExist) return self::$instance;
        else {
            self::$instance = new Router();
            self::$instanceExist = true;
            return self::$instance;
        }
    }

    public function addRoute($path, $htttpMethod, $protected, $callback)
    {
        $this->routes[strtoupper($htttpMethod)][$path] = array($protected, $callback);
    }

    public function resolve()
    {        
        $mimi = MimiApp::getInstance();
        $htttpMethod = $mimi->request->getMethod();
        $path = $mimi->request->getPath();
        $mimi->logs->writeMessage("urlLog", "Resolving URL: $path");

        if (isset($this->routes[$htttpMethod][$path])) {
            $protected = $this->routes[$htttpMethod][$path][0];
            $callback = $this->routes[$htttpMethod][$path][1];

            if ($protected) {
                $headers = getallheaders();
                if (array_key_exists('Authorization', $headers)) {
                    $bearerToken = substr($headers['Authorization'], 7);
                    $jwtTokenCrm = new JwtTokenCrm(["token"=>$bearerToken]);
                    if ($jwtTokenCrm->isExists()) {
                        $tokenPayload = MimiJWt::decodeJwt($bearerToken);
                        if (empty($tokenPayload["error"])) {
                            $mimiSystem = new MimiSystem(["name"=>$tokenPayload["sys"], "active"=>1]);                    
                            if ($mimiSystem->isExists()) {
                                $payload = json_decode(file_get_contents('php://input'), true);
                                call_user_func($callback, $tokenPayload, $payload);
                            }
                            else ApiEndpoint::error(["error"=>"Authentication failed!"], 401);                    
                        }
                        else ApiEndpoint::error(["error"=>"Authentication failed!"], 401);
                    }
                    else ApiEndpoint::error(["error"=>"Authentication failed!"], 401);
                }                
            }
            else {
                call_user_func($callback);
            }
        } else {
            http_response_code(404);
            echo "Route not found!";
        }
    }
}