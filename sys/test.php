<?php
require_once __DIR__ .'/vendor/autoload.php';

use app\engine\WebApp;

$app = WebApp::getInstance();

$app->logger->writeMessage("Elso uzenet");
$app->logger->writeMessage("Masodik uzenet");
$app->logger->writeMessage("Harmadik uzenet");
