<?php
namespace Lazarusphp\SchemaBuilder;

use LazarusPhp\LazarusBridge\SchemaValidator;
use ReflectionClass;
use LazarusPhp\SchemaBuilder\Interfaces\SchemaLoaderInterface;
use LazarusPhp\SchemaBuilder\Schema;

class SchemaLoader
{

    private SchemaLoaderInterface $schemaLoaderInterface;
    private $validator;

    // Set Constructor

    public function __construct($schema, $method)
    {

        $classname = $this->classname($schema);
        $this->validator = new SchemaValidator();

        define("TABLE",strtolower($classname->getShortName()));
            $this->schemaLoaderInterface = new $schema();

            // Validate the class exists
            if (class_exists($schema)) {

                $supportedMethods = ["up","alter","down"];


                if (in_array($method,$supportedMethods)){

                    if ($method === "up") {
                        $this->migrateTable($method);
                    }

                    if ($method === "alter") {
                        $this->alterTable($method);
                    }

                    // if ($method === "down") {
                        $this->dropTable($method);
                    // }
                }
                else
                {
                    echo "failed to call method";
                }
            } else {
                // Set Error Message Here
                "Error Finding CLass";
            }
        
    }


    // check if Script has body or is empty

    private function hasbody($schema, $methodname)
    {
        $reflection = new ReflectionClass($schema);
        if ($reflection->hasMethod($methodname)) {
            $method = $reflection->getMethod($methodname);
            // Check if the method is user-defined (not abstract or interface)
            if ($method->getFileName() !== false && $method->getStartLine() !== $method->getEndLine()) {
                // Check if the method has a non-empty body
                $filename = $method->getFileName();
                $startLine = $method->getStartLine();
                $endLine = $method->getEndLine();
                $lines = file($filename);
                $methodBody = implode("", array_slice($lines, $startLine, $endLine - $startLine - 1));
                // Remove whitespace and braces
                $methodBodyStripped = trim(str_replace(['{', '}'], '', $methodBody));
                if ($methodBodyStripped === '') {
                    return false;
                }
                return true;
            } else {
                return false;
            }
        }
    }


     public static function load(string $dir, string $method, string  $target = "")
    {
        if (!is_dir($dir)) {
            throw new \Exception("Directory not found: $dir");
        }

        // Scan all files.
        $files = scandir($dir);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            // Only handle PHP files
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            $filename = pathinfo($file, PATHINFO_FILENAME);
            if (!$filename) {
                continue;
            }

            // If a target is provided, only process that one
            if ($target !== '' && strtolower($filename) !== strtolower($target)) {
                continue;
            }

            $class = "Migrations\\Schemas\\$filename";

            if (!class_exists($class)) {
                throw new \Exception("Class $class does not exist.");
            }

            return new self($class, $method);
        }
    }

    // Get Refelection classname

      public function classname($classname)
    {
        return new ReflectionClass($classname);
    }


    // Set Migrate Table
     private function migrateTable($method)
    {

            if (method_exists($this->schemaLoaderInterface, $method)) {

                $schema = new Schema();
                // echo "Method Exists";
                if ($this->hasbody($this->schemaLoaderInterface, $method)) {
                    $this->schemaLoaderInterface->up($schema);
                } 
                else {
                    // ::generate("Cannot create table ".TABLE, ["reason" => "$method code has not Body in it"]);
                }
        }
    }
    
    // Set ALter table
        private function alterTable($method)
    {
        $schema = new Schema();
        if ($this->validator->hasTable(TABLE)) {
            if (method_exists($this->schemaLoaderInterface, $method)) {

                if ($this->hasbody($this->schemaLoaderInterface, $method)) {
                    $this->schemaLoaderInterface->alter($schema);
                } 
        
            } else {
                // SchemaErrors::generate("Cannot Alter table TABLE", ["reason" => "Method $method does not exist"]);
            }
        } else {
            // SchemaErrors::generate("Cannot Alter table TABLE", ["reason" => "Table TABLE is reqired"]);
        }
    }

        private function dropTable($method)
    {
        // $schema = new Schema();
        // if ($this->validator->hasTable(TABLE)) {
        //     if (method_exists($this->schemaLoaderInterface, $method)) {

        //         if ($this->hasbody($this->schemaLoaderInterface, $method)) {
        //             $this->schemaLoaderInterface->down($schema);
        //         } 
        
        //     } else {
        //         SchemaErrors::generate("Cannot Alter table TABLE", ["reason" => "Method $method does not exist"]);
        //     }
        // } else {
        //     SchemaErrors::generate("Cannot Alter table TABLE", ["reason" => "Table TABLE is reqired"]);
        // }
    }

}