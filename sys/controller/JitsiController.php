<?php

namespace Mimi\controller;

class JitsiController {

    public static function startVideoRecording($tokenPayload, $payload){
        echo json_encode($payload);
        echo json_encode($tokenPayload);
        die;
    }

}