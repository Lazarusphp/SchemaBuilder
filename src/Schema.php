<?php
namespace LazarusPhp\SchemaBuilder;
use LazarusPhp\LazarusBridge\DbQueries;
use LazarusPhp\LazarusBridge\SchemaValidator;
use LazarusPhp\SchemaBuilder\Blueprint;
class Schema extends DbQueries
{

    // Obtain tableName from SchemaLoader.php
    

    // 
    public function create(callable $blueprint)
    {
        $validator = new SchemaValidator();
        if($validator->hasTable(TABLE) === false){
        $sql = "CREATE TABLE IF NOT EXISTS ".TABLE." (";
    if (is_callable($blueprint)) {
            $class = new Blueprint();
            $blueprint($class);
             $sql .= $class->build();
        }

        $sql .= ")";

        echo $sql;
        $this->save($sql);
        }
    }

    public function alter(string | callable $blueprint)
    {
        $validator = new SchemaValidator();
        if($validator->hasTable(TABLE) === true)
        {
        $sql = "ALTER TABLE ".TABLE." ";
        if(is_string(($blueprint)))
        {
            $class = new Blueprint();
            $blueprint($class);
            $class->build;
        }
        if(is_callable($blueprint))
        {
            $class = new Blueprint();
            $blueprint($class);
            $sql .= $class->build();
        }

        echo $sql;
        $this->save($sql);
        }
    }



}