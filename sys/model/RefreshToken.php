<?php

namespace Mimi\model;

class RefreshToken extends DataModel {

    use TokenUtil;
    
    function __construct($filter=null){
        parent::__construct("refresh_tokens_crm", $filter);
    }

    function __destruct() {
        $this->deleteExpiredTokens();
    }
}