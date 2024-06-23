<?php

namespace Mimi\view;

use Mimi\engine\MimiApp;

class View
{
    public static function renderHtml($htmlFileName): void
    {
        $mimi = MimiApp::getInstance();

        $htmlHeaderFileName = $mimi->getWebDir()."/html/header.html";
        $fullHtmlFileName = $mimi->getWebDir() . "/html/".$htmlFileName;

        include $htmlHeaderFileName;
        include $fullHtmlFileName;
        exit();
    }
}