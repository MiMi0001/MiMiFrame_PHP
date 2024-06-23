<?php

namespace Mimi\engine\utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\InvalidArgumentException;
use Firebase\JWT\DomainException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\UnexpectedValueException;

use Mimi\engine\MimiApp;

class MimiJWT {
    const TOKEN_LIFETIME = '+30 minutes';
    const REFRESH_TOKEN_LIFETIME = '+8 hours';
    const ISSUER = "MimiFramw";

    public static function getTokens($mimiSystemName, $identificationProcedureId) {
        $mimi = MimiApp::getInstance();

        $secretKey  = $mimi->config->getConfig()["secretKey"];
        $refershSecretKey  = $mimi->config->getConfig()["refreshSecretKey"];

        $jwt = self::generateJwt($mimiSystemName, $identificationProcedureId, $secretKey, self::TOKEN_LIFETIME);
        $refershToken = self::generateJwt($mimiSystemName, $identificationProcedureId, $refershSecretKey, self::REFRESH_TOKEN_LIFETIME);

        return ["jwt"=>$jwt["token"], "refresh"=>$refershToken["token"], "jwtExpire"=>$jwt["expire"], "refreshTokenExpire"=>$refershToken["expire"]];
    }

    public static function generateJwt($mimiSystemName, $identificationProcedureId, $secretKey, $lifetime) {    
        $issuedAt   = new \DateTimeImmutable();
        $expire     = $issuedAt->modify($lifetime)->getTimestamp();
        $issuer     = self::ISSUER;
        $username   = "username";                                           

        $payload = [
            'iat'  => $issuedAt->getTimestamp(),            // Issued at: time when the token was generated
            'iss'  => $issuer,                              // Issuer
            'nbf'  => $issuedAt->getTimestamp(),            // Not before
            'exp'  => $expire,                              // Expire
            'sys' => $mimiSystemName,                      // mimi system's name
            'procid' => $identificationProcedureId          // The identification procedure's id
        ];

        $jwt = JWT::encode($payload, $secretKey, 'HS256');

        return ["token"=>$jwt, "expire"=>$expire];
    }

    public static function refreshToken($refreshToken) {
        $result = [];
        $mimi = MimiApp::getInstance();
        $refershSecretKey  = $mimi->config->getConfig()["refreshSecretKey"];        

        try {
            $decoded = (array) JWT::decode($refreshToken, new Key($refershSecretKey, 'HS256'));

            $issuedAt   = new \DateTimeImmutable();
            $expire     = $issuedAt->modify(self::REFRESH_TOKEN_LIFETIME)->getTimestamp();

            $decoded["iat"] = $issuedAt->getTimestamp();
            $decoded["nbf"] = $issuedAt->getTimestamp();
            $decoded["exp"] = $expire;

            $newToken = JWT::encode($decoded, $refershSecretKey, 'HS256');

            if ($decoded["iss"]===self::ISSUER) $result = ["jwt"=>$newToken, "jwtExpire"=>$expire];
            else $result = ["error"=>"Issuer is invalid!"];
        }
        catch (InvalidArgumentException $e) {
            $result["error"] = "InvalidArgumentException";
            $result["error"]["message"] = $e->getMessage();            
             // provided key/key-array is empty or malformed.
        } 
        catch (DomainException $e) {
            $result["error"] = "DomainException";
            $result["error"]["message"] = $e->getMessage();
            // provided algorithm is unsupported OR
            // provided key is invalid OR
            // unknown error thrown in openSSL or libsodium OR
            // libsodium is required but not available.
        } 
        catch (SignatureInvalidException $e) {
            $result=["error"=>"SignatureInvalidException"];
            $result["error"]["message"] = $e->getMessage();
            // provided JWT signature verification failed.
        } 
        catch (BeforeValidException $e) {
            $result=["error"=>"BeforeValidException"];
            $result["error"]["message"] = $e->getMessage();
            // provided JWT is trying to be used before "nbf" claim OR
            // provided JWT is trying to be used before "iat" claim.
        } 
        catch (ExpiredException $e) {
            $result=["error"=>"ExpiredException"];
            $result["error"]["message"] = $e->getMessage();
            // provided JWT is trying to be used after "exp" claim.
        } 
        catch (UnexpectedValueException $e) {
            $result=["error"=>"UnexpectedValueException"];
            $result["error"]["message"] = $e->getMessage();
            // provided JWT is malformed OR
            // provided JWT is missing an algorithm / using an unsupported algorithm OR
            // provided JWT algorithm does not match provided key OR
            // provided key ID in key/key-array is empty or invalid.
        }
        
        if (!empty($result["error"])) $mimi->logs->writeMessage("tokenErrors", $result["error"]." : ".$result["error"]["message"], "MimiJWT::refreshToken");

        return $result;               
    }

    public static function decodeJwt($token){
        $mimi = MimiApp::getInstance();

        $secretKey  = $mimi->config->getConfig()["secretKey"];
        try{
            $decodedToken = (array) JWT::decode($token, new Key($secretKey, 'HS256'));

            if ($decodedToken["iss"] === self::ISSUER) $result = $decodedToken;
            else $result = ["error"=>"Issuer is invalid!"];
        }
        catch (InvalidArgumentException $e) {
            $result["error"] = "InvalidArgumentException";
            $result["error"]["message"] = $e->getMessage();            
             // provided key/key-array is empty or malformed.
        } 
        catch (DomainException $e) {
            $result["error"] = "DomainException";
            $result["error"]["message"] = $e->getMessage();
            // provided algorithm is unsupported OR
            // provided key is invalid OR
            // unknown error thrown in openSSL or libsodium OR
            // libsodium is required but not available.
        } 
        catch (SignatureInvalidException $e) {
            $result=["error"=>"SignatureInvalidException"];
            $result["error"]["message"] = $e->getMessage();
            // provided JWT signature verification failed.
        } 
        catch (BeforeValidException $e) {
            $result=["error"=>"BeforeValidException"];
            $result["error"]["message"] = $e->getMessage();
            // provided JWT is trying to be used before "nbf" claim OR
            // provided JWT is trying to be used before "iat" claim.
        } 
        catch (ExpiredException $e) {
            $result=["error"=>"ExpiredException"];
            $result["error"]["message"] = $e->getMessage();
            // provided JWT is trying to be used after "exp" claim.
        } 
        catch (UnexpectedValueException $e) {
            $result=["error"=>"UnexpectedValueException"];
            $result["error"]["message"] = $e->getMessage();
            // provided JWT is malformed OR
            // provided JWT is missing an algorithm / using an unsupported algorithm OR
            // provided JWT algorithm does not match provided key OR
            // provided key ID in key/key-array is empty or invalid.
        }

        if (!empty($result["error"])) $mimi->logs->writeMessage("tokenErrors", $result["error"]." : ".$result["error"]["message"], "MimiJWT::decodeJwt");
        
        return $result;
    }
}