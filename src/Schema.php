<?php
namespace LazarusPhp\SchemaBuilder;
use LazarusPhp\LazarusBridge\DbQueries;
use LazarusPhp\SchemaBuilder\Blueprint;
class Schema extends DbQueries
{

    // Obtain tableName from SchemaLoader.php
    

    // 
    public static function create(callable $blueprint)
    {
        $sql = "CREATE TABLE IF EXISTS (";
    if (is_callable($blueprint)) {
            $class = new Blueprint();
            $blueprint($class);
             $sql .= $class->build();
        }

        $sql .= ")";

        $this->save($save);
    }

    public static function alter(callable $blueprint)
    {
        $sql = "ALTER TABLE ".TABLE." "
        if(is_callable($blueprint))
        {
            $class = new Blueprint();
            $blueprint($class);
            $class->build();
        }
    }


}