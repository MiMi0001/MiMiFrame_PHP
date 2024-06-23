<?php

namespace Mimi\view;

class ApiEndpoint {
    public static function success($payload){
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        die;        
    }

    public static function error($payload, $responseCode) {
        http_response_code($responseCode);
        echo json_encode($payload);
        die;
    }
}