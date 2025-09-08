<?php
namespace LazarusPhp\SchemaBuilder\CoreFiles;

use LazarusPhp\LazarusBridge\SchemaValidator;

class BlueprintBuilder
{

    // Index properties
    protected $name = "";
    protected static $params = [];
    protected static $query = [];
    protected static $validator;

    protected static $primaryKey = [];
    protected static $index = [];
    protected $ai = [];
    protected $requirePrimary = false;

    // Modifier Properties

    private $oldname = "";
    private $rename;


    // Foreign Keys $properties

    protected static array  $fk = [];
    protected static $fkName;
    protected static $method;
    protected static $constraint = [];

    protected static $countIndex = [];


    public function __construct()
    {
        self::$validator = new SchemaValidator();
    }

    private function sendRequest($name, $type, $actions)
    {
        $this->processRequest($name, $type, $actions);
    }

    private function validateFunction(string $functionName,array $array)
    {
        if(in_array($functionName,$array))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    private function getType(string $functionName)
    {
        if($this->validateFunction($functionName,["int","bigint","tinyint","string","varchar","text","mediumtext","longtext","data","datetime"]))
        {
            return "datatype";
        }

        if($this->validateFunction($functionName,["nullable","notNullable"]))
        {
            return "nullable";
        }

        if($this->validateFunction($functionName,["default","now"]))
        {
            return "default";
        }

        if($this->validateFunction($functionName,["unsigned","binary"]))
        {
            return "attributes";
        }

        if($this->validateFunction($functionName,["ai"]))
        {
            return "ai";
        }

        if($this->validateFunction($functionName,["primary"]))
        {
            return "primary";
        }

        
        if($this->validateFunction($functionName,["index"]))
        {
            return "indexes";
        }
        
        if($this->validateFunction($functionName,["uniques"]))
        {
            return "uniques";
        }

        if($this->validateFunction($functionName,["first","after"]))
        {
            return "position";
        }
        
        if($this->validateFunction($functionName,["foreignKey"]))
        {
            return "fk";
        }

        if($this->validateFunction($functionName,["onUpdate"]))
        {
            return "fkUpdate";
        }
        
        if($this->validateFunction($functionName,["onDelete"]))
        {
            return "fkDelete";
        }
        
        if($this->validateFunction($functionName,["reference"]))
        {
            return "fkReference";
        }

    }

       protected static function params(string $name, string $action, array $array): void
    {
        // Initialize the table if not already set
        if (!isset(self::$params[TABLE])) {
            self::$params[TABLE] = [];
        }

        if (!isset(self::$params[TABLE][$name])) {
            self::$params[TABLE][$name] = [];
        }

        if (!isset(self::$params[TABLE][$name][$action])) {
            self::$params[TABLE][$name][$action] = [];
        }
        // Merge with existing column params if they exist

        if (isset(self::$params[TABLE][$name][$action])) {
            self::$params[TABLE][$name][$action] = array_merge(
                self::$params[TABLE][$name][$action],
                $array
            );
        } else {
            self::$params[TABLE][$name][$action] = $array;
        }
    }

    protected function processRequest($name, $action, $array)
    {
        $this->name = $name;
        self::params($this->name, $action, $array);
    }


     

    // Datatypes

     public function id($id = "id")
    {
        return $this->int($id)->ai();
    }

    public function string($name, $value = 100)
    {  
        $function = __FUNCTION__;
        $actions = [
            "function" => $function,
            "name" => $name,
            "value" => $value,
            "command" => "$name CHAR($value)",
        ];

        $type = $this->getType($function);
       $this->sendRequest($name,$type,$actions);
        return $this;
    }

    public function varchar($name, $value = 255)
    {
        $function = __FUNCTION__;
        $actions = [
            "function" => $function,
            "name"=>$name,
            "value" => $value,
            "command" => "$name VARCHAR($value)",
        ];
        
       $type = $this->getType($function);
       $this->sendRequest($name,$type,$actions);
        return $this;
    }

    public function tinyint($name)
    {
        $function = __FUNCTION__;
        $actions = [
            "function" => $function,
            "name"=>$name,
            "command" => "$name TINYINT ",
        ];

        $type = $this->getType($function);
        $this->sendRequest($name, $type, $actions);
        return $this;
    }

    public function int($name)
    {
        $function = __FUNCTION__;
        $actions = [
            "function" => $function,
            "name"=>$name,
            "command" => "$name INT ",
        ];

        $type = $this->getType($function);
        $this->sendRequest($name, $type, $actions);
        return $this;
    }


    public function bigint($name)
    {
        $function = __FUNCTION__;
        $actions = [
            "function" => $function,
            "name" => $name,
            "command" => "$name BIGINT ",
        ];

         $type = $this->getType($function);
        $this->sendRequest($name, $type, $actions);
        return $this;
    }


    public function text($name)
    {  
        $function = __FUNCTION__;
        $actions = [
            "function" => $function,
            "name" => $name,
            "command" => "$name TEXT ",
        ];

         $type = $this->getType($function);
        $this->sendRequest($name, $type, $actions);
        return $this;
    }

    public function mediumText($name)
    {  
        $function = __FUNCTION__;
        $actions = [
            "function" => $function,
            "name" => $name,
            "command" => "$name MEDIUMTEXT ",
        ];

         $type = $this->getType($function);
        $this->sendRequest($name, $type, $actions);
        return $this;
    }

    public function longText($name)
    {  
        $function = __FUNCTION__;
        $actions = [
            "function" => $function,
            "name" => $name,
            "command" => "$name LONGTEXT ",
        ];

         $type = $this->getType($function);
        $this->sendRequest($name, $type, $actions);
        return $this;
    }

    public function date($name)
    {
        $function = __FUNCTION__;
        $actions = [
            "function" => $function,
            "name" => $name,
            "command" => "$name DATE ",
        ];

         $type = $this->getType($function);
        $this->sendRequest($name, $type, $actions);
        return $this;
    }

    public function dateTime($name)
    {

        $function = __FUNCTION__;
        $actions = [
            "function" => $function,
            "name" => $name,
            "command" => "$name DATETIME ",
        ];

         $type = $this->getType($function);
        $this->sendRequest($name, $type, $actions);
        return $this;
    }


    // End Datatypes

    // Defaults

    // Defaults
    public function now($astimestamp = true)
    {

            $function = __FUNCTION__;
            if ($astimestamp === false) {
                $value = "CURRENT_TIMESTAMP";
                $actions = ["function"=>$function,"command"=>"DEFAULT $value"];
                $type = $this->getType($function);
                $this->sendRequest($this->name, $type, $actions);
            } 
            else {
                $this->default(Functions::now());
            }
        return $this;
    }

    public function default(string|int $value)
    {
        if (empty($value)) {
            echo "Value cannot be empty";
        } else {
            if ($this->processDefaults()) {
            //    Check if value is a string replace Quotes
                if (is_string($value)) {
                    $value = "'" . str_replace("'", "''", $value) . "'";
                }

                // Set Array and naming convention
                $function = __FUNCTION__;
                $actions = ["function"=>$function,"command"=>"DEFAULT $value"];
                $type = $this->getType($function);
                $this->sendRequest($this->name, $type, $actions);
            }
        }

        return $this;
    }
    // End Defaults


    // Attributes

    public function unsigned()
    {
        $function = __FUNCTION__;
        $actions = [
            "function"=>$function,
            "requiredDatatype" => "int|bigint|tinyint",
            "command"=>" UNSIGNED "
        ];
        $type = $this->getType($function);
        $this->sendRequest($this->name, $type, $actions);
        return $this;
    }

    public function binary()
    {
        $function = __FUNCTION__;
        $actions = [
            "function"=>$function,
            "requiredDatatype" => "char|varchar|text|mediumtext|longtext",
            "command"=>" BINARY "
        ];
            $type = $this->getType($function);
            $this->sendRequest($this->name, $type, $actions);
        return $this;
    }

    public function currentTimestamp()
    {
        $function = __FUNCTION__;
        $actions = [
            "function"=>$function,
            "requiredDatatype" => "datetime",
            "command"=>" on update CURRENT_TIMESTAMP "
        ];
            $type = $this->getType($function);
            $this->sendRequest($this->name, $type, $actions);
        return $this;
    }

    // End Attributes

    // Nullable

    public function nullable()
    {

          $function = __FUNCTION__;
        $actions = [
            "function"=>$function,
            "isActive" => true,
            "command"=>" NULL "
        ];
        $type = $this->getType($function);
        $this->sendRequest($this->name, $type, $actions);
        return $this;
    }

    public function notNullable()
    {
        
         $this->processRequest($this->name,"nullable",[
            "command"=>" NOT NULL "
        ]);
        return $this;
    }

    // End Nullable


    // Position


  public function after($name)
  {
    // $table = TABLE;
    // $validator = new SchemaValidator($table);
    // if ($validator->hasTable() === false) {
    //   if (!$validator->validField("column_name", $name)) {
    //     SchemaErrors::generate("Cannot Adjust Position", ["reason" => "Table $table doesnt exist"]);
    //     return false;
    //   } elseif ($validator->hasTable($table)) {
    //     if (!$validator->validField("column_name", $name)) {
    //       SchemaErrors::generate("Cannot Adjust Position", ["reason" => "Table $table doesnt exist"]);
    //       return false;
    //     }
    //   }
    // } else {

    // $actions = ["function"=>$function,"command" => " AFTER "];
    // $type = $this->getType($function);
    // $this->sendRequest($name, $type, $actions);
    //   return $this;
    // }
  }


  public function first()
  {
    // $table = TABLE;
    // $validator = new SchemaValidator($table);
    // if ($validator->hasTable($table) === false) {
    //   if (!$validator->validField("column_name", $name)) {
    //     SchemaErrors::generate("Cannot Adjust Position", ["reason" => "Table $table doesnt exist"]);
    //     return false;
    //   } elseif ($validator->hasTable($table)) {
    //     if (!$validator->validField("column_name", $name)) {
    //       SchemaErrors::generate("Cannot Adjust Position", ["reason" => "Table $table doesnt exist"]);
    //       return false;
    //     }
    //   }
    // } else {
    
    // $function = __FUNCTION__;
    // $actions = ["function"=>$function,"command" => " FIRST "];
    // $type = $this->getType($function);
    // $this->sendRequest($name, $type, $actions);
    // }
    // return $this;
  }

    // End Positions

    // Indexes Primary keys and Foreign Keys


 

    public function ai()
    {
        $function = __FUNCTION__;
        $actions = [
            "function"=>$function,
            "command" => " AUTO_INCREMENT "
        ];

        $type = $this->getType($function);
        $this->sendRequest($this->name,$type,$actions);
        // Automatically Assign Primary
        $this->primary();
        return $this;
    }

    // Changed name from primaryKey to primary
    public function primary()
    {
        $function = __FUNCTION__;
        $id = ($this->name === "id") ? "id" : $this->name;
        $actions = ["command" => " PRIMARY KEY ($id) "];
        $type = $this->getType($function);
        $this->sendRequest($this->name,$type,$actions);
        return $this;
    }

    public function index($key = "idx_default",...$args)
    {
        self::$index = $key;
        $function = __FUNCTION__;
        // do a check if in modify mode add index here
         // Validate that the name and the add command cannot be used together
        if(count($args) === 0){
        $value="";
        }
        else{
        $value = count($args) === 1 ? (string) $args[0] : $args;
        }
        $command = "INDEX";
        $actions = [
            $key => [
                "function"=>$function,
                "name" => self::$index,
                "value"=>$value,
                "command"=>" $command ",
                "reference" => self::$index
            ]
        ];
        $type = $this->getType($function);
        $this->sendRequest($this->name,$type,$actions);
    }


    public function unique($key = "idx_default",...$args)
    {
        $function = __FUNCTION__;
        self::$index = $key;
        // do a check if in modify mode add index here
         // Validate that the name and the add command cannot be used together
        if(count($args) === 0){
        $value="";
        }
        else{
        $value = count($args) === 1 ? (string) $args[0] : $args;
        }

        $actions = [
            $key => [
                "name" => self::$index,
                "value"=>$value,
                "command"=>" UNIQUE ",
                "reference" => self::$index,
            ],
        ];

        $type = $this->getType($function);
        $this->sendRequest($this->name,$type, $actions);
    }
    // End Indexes primary keys

    // FOreign keys
    
    public function foreignKey(string $column,$constraint=false)
    {
        if(!in_array($column,self::$constraint))
        {
            self::$constraint[] = $column;
        }
        self::$fkName = $column;
        $constraint = ($constraint===true) ? true : false;
        $actions = ["table"=>TABLE,"column"=>$column,"constraint"=>$constraint];
         $function = __FUNCTION__;
         $type = $this->getType($function);
         $this->sendRequest(self::$fkName,$type,$actions);

        return $this;
    }

    public function reference($refTable,$refColumn)
    {
        $function = __FUNCTION__;
        $actions = ["referenceTable"=>$refTable,"referenceColumn"=>$refColumn];
           $type = $this->getType($function);
         $this->sendRequest(self::$fkName,$type,$actions);
        return $this;
    }

   
    public function onUpdate($action = "cascade")
    {
        $this->actions($action);
        $actions = ["command"=>"$action"];
        $function = __FUNCTION__;
         $type = $this->getType($function);
         $this->sendRequest(self::$fkName,$type,$actions);
        return $this;
    }

    public function onDelete($action = "cascade")
    {
        $this->actions($action);
        $function = __FUNCTION__;
        $actions = ["command"=>"$action"];
         $type = $this->getType($function);
         $this->sendRequest(self::$fkName,$type,$actions);
        return $this;
    }

    private function actions(string $action)
    {
        $action = strtoupper($action);

        $supportedActions = [
            'CASCADE',
            'RESTRICT',
            'NULL',
            'NOACTION',
            'DEFAULT'
        ];


        if(in_array($action,$supportedActions))
        {switch($action)
        {
            case 'CASCADE':
                return "CASCADE";
            case 'RESTRICT':
                return "RESTRICT";
            case 'NULL':
                return "SET NULL";
            case 'NOACTION':
                return "NO ACTION";
            case 'DEFAULT':
                return "SET DEFAULT";
        }
    }
        else
        {
            echo "Unsupported Action $action";

        }
         
    }

    // End Foreign Keys






    /**
     * @description
     * Set of modifier classes designed to make changes to the Database when a table already exists
     * 
     */
    public function modify()
    {
        $function = __FUNCTION__;
        $actions = [
            "function"=>$function,
            "name"=>$this->name,
            "type"=>"modify",
            "command"=>" MODIFY "
        ];

        $type = $this->getType($function);
        $this->sendRequest($this->name,$type,$actions);
    }

    // public function rename($name)
    // {
    //     $validator = new SchemaValidator(TABLE);
    //     $index = $validator->hasIndexes();

    //     foreach($index as $index)
    //     {
    //         if($index->COLUMN_NAME === $name)
    //         {
    //             SchemaErrors::generate("Cannot Rename Table", ["Reason Column $name Belongs to an index"]);
    //             break;
    //         }
    //     }

    //     if($validator->hasTable(TABLE) && !$validator->hasColumn($name))
    //     {
    //         SchemaErrors::generate("Cannot Rename Table", ["Reason Column $name doesnt exist"]);
                
    //     }
    //     $this->rename = true;
    //     $this->oldname = $name;
    //     return $this;
    // }

    public function change()
    {
        $function = __FUNCTION__;
        $name  = ($this->oldname || !empty($this->oldname)) ? $this->oldname : $this->name;
        $actions = [
            "modify"=>true,
            "name"=>$this->name,
            "type"=>"change",
            "command"=>" CHANGE {$name} "
        ];
          $type = $this->getType($function);
         $this->sendRequest(self::$fkName,$type,$actions);
    }

    // public function add()
    // {
    
    //         $name = $this->name;
    //         $actions = [
    //             "modifier"=>true,
    //             "name"=>$this->name,
    //             "type"=>"add",
    //             "command"=>" ADD "];
    //         $this->processRequest($this->name,"modifier",$actions);
    //     }
    //     else
    //     {
    //         // self::unsetParams($this->name);
    //         $this->modify();
    //     }
    // }

    // End Modifiers

}