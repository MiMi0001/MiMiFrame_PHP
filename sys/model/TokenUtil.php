<?php

namespace Mimi\model;

trait TokenUtil {
    public function deleteExpiredTokens() {
        $now  = new \DateTimeImmutable();
        $nowUnixTime = $now->getTimestamp();

        $query = "DELETE FROM $this->tableName WHERE expire<:expire";
        $queryParams = ["expire"=>$nowUnixTime];
        $statement = $this->pdo->prepare($query);
        $statement->execute($queryParams);
    }
}