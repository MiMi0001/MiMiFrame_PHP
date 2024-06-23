<?php

namespace Mimi\engine\router;

class Request
{
    private static bool $instanceExist = false;
    private static Request $instance;
    private string $url;



    private function __construct() {
        $this->url = $_SERVER["REQUEST_URI"];
    }

    public function getInstance(): Request {
        if (self::$instanceExist) return self::$instance;
        else {
            self::$instance = new Request();
            self::$instanceExist = true;
            return self::$instance;
        }
    }    

    public function get(string $variable)
    {
        if (array_key_exists($_REQUEST, $variable)) {
            return $_REQUEST[$variable];
        }
        return null;
    }

    public function getPath()
    {
        $parsedUrl = parse_url($this->url);
        return $parsedUrl["path"];
    }

    public function getHost() {
        $parsedUrl = parse_url($this->url,  PHP_URL_HOST);
        echo "getHost: ";
        var_dump ($this->url);
        return $parsedUrl["host"];
    }

    public function getMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function isGet(): bool
    {
        return $this->getMethod() === 'get';
    }
    public function isPost(): bool
    {
        return $this->getMethod() === 'post';
    }

    public function getBody(): array
    {
        $body = [];
        if ($this->getMethod() === 'get'){
            foreach ($_GET as $key => $value){
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->getMethod() === 'post'){
            foreach ($_POST as $key => $value){
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $body;
    }

    public function redirect(string $url)
    {
        header("Location:".$url);
    }
}