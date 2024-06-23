<?php

namespace Mimi\controller;

use Mimi\view\View;

class StartPageController
{
    public static function render(){
        View::renderHtml("start-page.html");
    }
}