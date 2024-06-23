<?php
namespace Mimi;

use Mimi\engine\MimiApp;
use Mimi\engine\utils\MimiJWT;
use Mimi\controller\StartPageController;
use Mimi\controller\TestController;
use Mimi\controller\MimiSystemController;
use Mimi\controller\JitsiController;

$mimi = MimiApp::getInstance();

// URL routok listája
$mimi->router->addRoute("/", "GET", false, [StartPageController::class, "render"]);
$mimi->router->addRoute("/test", "GET", false, [TestController::class, "render"]);

$mimi->router->addRoute("/api/mimisytem/login", "POST", false, [MimiSystemController::class, "login"]);
$mimi->router->addRoute("/api/mimisytem/refresh-token", "POST", false, [MimiSystemController::class, "refreshToken"]);
$mimi->router->addRoute("/api/jitsi/start-video-recording", "POST", true, [JitsiController::class, "startVideoRecording"]);

// /api/user/get
// /api/identify/start ...

$mimi->run();