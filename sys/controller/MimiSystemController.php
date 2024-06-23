<?php

namespace Mimi\controller;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Mimi\engine\MimiApp;
use Mimi\engine\utils\MimiJWT;
use Mimi\model\MimiSystem;
use Mimi\model\RefreshTokenCrm;
use Mimi\model\JwtTokenCrm;
use Mimi\view\ApiEndpoint;

class MimiSystemController {

    public static function login() {        
        $mimi = MimiApp::getInstance();

        $payload = json_decode(file_get_contents('php://input'), true);

        if ( !empty($payload["mimiSystemName"]) && !empty($payload["identificationProcedureId"]) && !empty($payload["password"]) ) {
            $mimiSystemName = $payload["mimiSystemName"];
            $identificationProcedureId = $payload["identificationProcedureId"];            
            $systemPassword = $payload["password"];

            $mimiSystem = new MimiSystem(["name"=>$mimiSystemName, "active"=>1]);
            $jwtToken = new JwtTokenCrm(); 
            $refreshToken = new RefreshTokenCrm(); 

            if ( $mimiSystem->isExists() && $mimiSystem->authenticate($systemPassword) ) {
                $tokens = MimiJWT::getTokens($mimiSystemName, $identificationProcedureId);

                $refreshToken->new(["token"=>$tokens["refresh"], "expire"=>$tokens["refreshTokenExpire"]]);
                $jwtToken->new(["token"=>$tokens["jwt"], "expire"=>$tokens["jwtExpire"]]);
                                            
                unset($tokens["jwtExpire"]);
                unset($tokens["refreshTokenExpire"]);
                ApiEndpoint::success($tokens);
            }
            else ApiEndpoint::error(["error"=>"Authentication failed!"], 401);
        }
    }

    public static function refreshToken() {
        $mimi = MimiApp::getInstance();

        $payload = json_decode(file_get_contents('php://input'), true);
        $refreshToken = $payload["refresh"];
        $refreshTokenObj = new RefreshTokenCrm(["token"=>$refreshToken]);

        if ($refreshTokenObj->isExists()) {
            $newToken = MimiJWT::refreshToken($refreshToken);
            if (empty($newToken["error"])) {
                $jwtTokenObj = new JwtTokenCrm();
                $jwtTokenObj->new(["token"=>$newToken["jwt"], "expire"=>$newToken["jwtExpire"]]);

                unset($newToken["jwtExpire"]);
                ApiEndpoint::success($newToken);
            }
            else ApiEndpoint::error(["error"=>"Authentication failed!"], 401);
        }
        else {            
            $mimi->logs->writeMessage("tokenErrors", "Refresh token not found in the database", "MimiSystemController::refreshToken");
            ApiEndpoint::error(["error"=>"Auethentication failed!"], 401);            
        }
    }
}