<?php

namespace Mimi\model;

class MimiSystem extends DataModel {
    public string $name;
    
    function __construct($filter=null){
        parent::__construct("mimi_systems", $filter);
    }

    public function authenticate($password){
        return password_verify($password, $this->fields["password"]);
    }
}