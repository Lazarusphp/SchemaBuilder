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
        $sql = "CREATE TABLE IF NOT EXISTS ".TABLE." (";
    if (is_callable($blueprint)) {
            $class = new Blueprint("create");
            $blueprint($class);
            $sql .= $class->build();
        }

        $sql .= ")";

        // echo $sql;
        $this->save($sql);
    }

    public function alter(callable $blueprint)
    {
      
        $sql = "ALTER TABLE ".TABLE." ";
        if(is_callable($blueprint))
        {
            $class = new Blueprint("alter");
            $blueprint($class);
            $sql .= $class->build();
        }
        $this->save($sql);
    }



}