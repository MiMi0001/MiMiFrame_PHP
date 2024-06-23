<?php

namespace Mimi\model;

use Mimi\engine\MimiApp;

abstract class DataModel {

    protected $id;
    protected string $tableName;
    protected $fields;
    protected MimiApp $mimi;
    protected \PDO $pdo;

    /**
    * Constructor
    *    
    * @param array $tableName Name of the database table for the model
    *
    * @return mixed integer for id, array for the sql WHERE statement.
    */
    function __construct($tableName, $filter=null) {
        $this->mimi = MimiApp::getInstance();
        $this->pdo = $this->mimi->pdo;        
        $this->tableName = $tableName;

        if (!empty($filter)) {
            if (gettype($filter) === "integer") $this->getFirst(["id"=>$id]);
            elseif (gettype($filter) === "array") $this->getFirst($filter);
        }        
    }

    public function getFields() {
        return $this->fields;

        // echo "<br> DataModel object: <br>";
        // var_dump($this);
        // echo "<br>";
        // echo "Datamodel::get(): <br>";
        // foreach ($this as $column=>$value) {
        //     echo "<br>";
        //     var_dump($column);
        // }

        // $reflect = new \ReflectionClass($this);
        // foreach($reflect->getProperties(\ReflectionProperty::IS_PUBLIC) as $props) {
        //     echo $props->getName() . ' : ' . $props->getValue($this) . '<br>';
        // }

    }

    public function isExists(){
        return !empty($this->fields);
    }

    /**
    * Gets a model's data from the database, and puts it in $this->fields property
    *    
    * @param array $filter Filters for the sql WHERE statement. e.g: ["name"=>"John Doe", "id"=>3];
    *
    * @return mixed Array of the query result ($this->fields) or bool false if the query returned no data
    */
    public function getFirst(array $filter) {
        $whereArray = [];
        $queryParams = [];
        foreach ($filter as $column=>$value) {
            $whereArray[] = "$column=:$column";            
            $queryParams[":$column"] = $value;
        }

        $where = implode(" AND ", $whereArray);
        
        $query = "SELECT * FROM $this->tableName WHERE $where;";
        $statement = $this->pdo->prepare($query);
        $statement->execute($queryParams);
        $dataSet = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (!empty($dataSet)) {
            $this->fields = $dataSet[0];
            return $this->fields;
        }        
        else return false;
    }

    public function new(array $fields, bool $insertNow=true) {
        $this->fields = $fields;

        if ($insertNow) {
            $columns = [];
            $placeholders = [];
            
            $queryParams = [];
            foreach ($fields as $column=>$value) {
                $columns[] = $column;
                $queryParams[":$column"] = $value;
            }

            $columns = implode(", ", $columns);
            $placeholders = implode(", ", array_keys($queryParams));

            $query = "INSERT INTO $this->tableName ($columns) VALUES ($placeholders)";
            $statement = $this->pdo->prepare($query);
            $statement->execute($queryParams);
        }

    }
}