<?php

namespace Mimi\model;

class JwtToken extends DataModel {

    use TokenUtil;
    
    function __construct($filter=null){
        parent::__construct("jwt_tokens_crm", $filter);
    }

    function __destruct() {
        $this->deleteExpiredTokens();
    }    
}