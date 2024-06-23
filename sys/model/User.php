<?php

namespace Mimi\model;

class User extends DataModel {
    public string $name;

    function __construct(int $id=null){
        parent::__construct("users", $id);

        // $query = 'SELECT * FROM users WHERE id=:id';
        // $queryParams = [":id"=>$id];

        // $statement = $this->pdo->prepare($query);
        // $statement->execute($queryParams);
        // $this->fields = $statement->fetch(\PDO::FETCH_ASSOC);
        
        // echo "<br> User fields: ";
        // var_dump ($this->fields);
    }
}