<?php

namespace LazarusPhp\SchemaBuilder;

use LazarusPhp\LazarusBridge\SchemaValidator;
use LazarusPhp\LazarusDb\SchemaBuilder\SchemaErrors;

use LazarusPhp\SchemaBuilder\CoreFiles\BlueprintBuilder;

class Blueprint extends BlueprintBuilder
{

    public static function getParams(...$args)
    {

        if (isset(self::$params[TABLE])) {
            if (count($args) === 0) {
                return self::$params[TABLE];
            } else {
                $table = array_merge(self::$params[TABLE], $args);
                return $table;
            }
        } else {
        }
    }


    public function __construct($method)
    {
        self::$method = $method;
        parent::__construct();
    }


    /**
     * @method method() 
     * @param string $method
     * @static
     *  method is designed to set a method using a  passed parameter and then ouptut when no param is passed
     * */
    public function method(string $method = "")
    {
        if (empty($method)) {
            if (isset(self::$method[TABLE])) {
                return self::$method[TABLE];
            }
        } else {
            if (!isset(self::$method)) {
                self::$method[] = TABLE;
            }
            self::$method[TABLE] = $method;
        }
    }

    protected static function unsetParams($name)
    {
        if (isset(self::$params[TABLE][$name])) {
            unset(self::$params[TABLE][$name]);
        }
        return self::$params[TABLE];
    }


    private static function passDatatype($props)
    {
        $datatype = isset($props["datatype"]) ? $props["datatype"] : null;
        if ($datatype) {
            return $datatype["command"];
        }
    }

    private static function passAi($props)
    {
        if (isset($props["ai"])) {
            return $props["ai"]["command"];
        }
    }

    private static function passModifier($props)
    {
        if (isset($props["modifier"])) {
            if (isset($props["modifier"]["command"])) {
                return $props["modifier"]["command"];
            }
        }
    }

    private static function passNullable($props)
    {
        if (isset($props["nullable"])) {
            return $props["nullable"]["command"];
        }
    }

    private static function passDefault($props)
    {
        if (isset($props["default"])) {
            return $props["default"]["command"];
        }
    }

    private static function passAttributes($props)
    {
        if (isset($props["attributes"])) {
            $required = $props["attributes"]["requiredDatatype"];
            $exploded = explode("|", $required);

            if (!in_array($props["datatype"]["function"], $exploded)) {
                SchemaErrors::generate("Cannot Pass Attributes", [
                    "Reason" => "Invalid Datatype used",
                    "Supported Types" => $exploded
                ]);
                return false;
            } else {
                return $props["attributes"]["command"];
            }
        } else {
            return "";
        }
    }
    private static function passPosition($props)
    {
        if (isset($props["position"])) {
            return $props["position"]["command"];
        }
    }

    private static function passPrimary($props)
    {
        if (isset($props["primary"])) {
            self::$query["primary"][] = $props["primary"]["command"];
        }
    }


    private static function passIndexes()
    {
        $references = [];
        $commands = [];
        $params = self::getParams();



        foreach ($params as $table => $properties) {
            if (isset($properties["indexes"])) {
                $props = $properties["indexes"];
                foreach ($properties["indexes"] as $ref => $data) {
                    $index = self::$validator->hasIndexes($data["name"]);
                    if (self::$method === "alter" && $index === true) {
                        self::$query["indexes"][] = " DROP INDEX {$data["name"]}";
                    }

                    if (is_array($data["value"])) {
                        $idxValues = implode(", ", $data["value"]);
                    } else {
                        $idxValues = $data["value"];
                    }

                    $references[$ref][] = $idxValues;

                    $commands[$ref] =  $data["command"];
                }
            }
        }

        foreach ($commands as $idxName => $command) {
            $command = (self::$method === "alter") ? " ADD $command" : $command;
            $columnList = implode(', ', array_unique($references[$idxName]));
            $queries = "$command $idxName($columnList)";
            self::$query["indexes"][] = $queries;
        }
    }

    private static function passUniques()
    {
        $references = [];
        $commands = [];
        $params = self::getParams();

        foreach ($params as $table => $properties) {
            if (isset($properties["uniques"])) {
                $props = $properties["uniques"];
                foreach ($properties["uniques"] as $ref => $data) {

                    if (self::$method === "alter" && self::$validator->hasIndexes($data["name"] === true)) {
                        self::$query["uniques"][] = " DROP INDEX {$data["name"]}";
                    }

                    if (is_array($data["value"])) {
                        $idxValues = implode(", ", $data["value"]);
                    } else {
                        $idxValues = $data["value"];
                    }

                    $references[$ref][] = $idxValues;

                    $commands[$ref] =  $data["command"];
                }
            }
        }

        foreach ($commands as $idxName => $command) {
            $command = (self::$method === "alter") ? " ADD $command" : $command;
            $columnList = implode(', ', array_unique($references[$idxName]));

            $queries = "$command $idxName($columnList)";
            self::$query["uniques"][] = $queries;
        }
    }

    public static function passfk()
    {

        $reference = [];
        $params = array_filter(self::getParams(), function ($properties) {
            return isset($properties["fk"])
                && isset($properties["fkReference"])
                && !empty($properties["fkReference"]["referenceTable"])
                && !empty($properties["fkReference"]["referenceColumn"]);
        });

        foreach ($params as $index => $properties) {
            $props = $properties["fk"] ?? "";
            $constraint = $properties["fk"]["constraint"] ?? "";
            $table = $props["table"] ?? "";
            $column = $props["column"] ?? "";

            $refTable = $properties["fkReference"]["referenceTable"] ?? "";
            $refColumn = $properties["fkReference"]["referenceColumn"] ?? "";
            $onDelete = $properties["fkDelete"]["command"] ?? "restrict";
            $onUpdate = $properties["fkUpdate"]["command"] ?? "restrict";

            (!in_array($table, $reference)) ? $reference[$index]["table"] = $table : "";
            (!in_array($column, $reference)) ? $reference[$index]["column"] = $column : "";
            (!in_array($constraint, $reference)) ? $reference[$index]["constraint"] = $constraint : "";
            (!in_array($refTable, $reference)) ? $reference[$index]["refTable"] = $refTable : "";
            (!in_array($table, $reference)) ? $reference[$index]["refColumn"] = $refColumn : "";
            (!in_array($onDelete, $reference)) ? $reference[$index]["onDelete"] = $onDelete : "";
            (!in_array($onUpdate, $reference)) ? $reference[$index]["onUpdate"] = $onUpdate : "";
        }

        $add = (self::$method === "alter") ? " ADD " : "";

        // Generate New Foreign Key.
        $foreignKey = [];
        if (self::$validator->hasForeignKey() === true) {
            $cnactive = [];
            foreach (self::$validator->getData("fk") as $cn) {
                foreach (self::$constraint as $c) {
                    if ($c === $cn->CONSTRAINT_NAME) {
                        $cnactive[$c] = $c;
                    }
                }
            }
        }


        foreach ($reference as $index => $command) {

            // Add |Foreign key validation here per id
            if (!isset($cnactive[$index])) {
                $constraintStr = ($constraint === true) ? "CONSTRAINT {$command["column"]} " : "";
                $foreignKey[$index] =
                    "$add $constraintStr FOREIGN KEY ({$command["column"]}) REFERENCES {$command["refTable"]}"
                    . "({$command["refColumn"]})"
                    . " ON UPDATE {$command["onUpdate"]} ON DELETE {$command["onDelete"]}";
            }
        }


        foreach ($foreignKey as $idxName => $command) {
            self::$query["fk"][] = $command;
        }
        // Functions::dd(self::$query["fk"]);
        // exit();
    }



    protected static function processParams()
    {
        $columns = [];
        $params = self::getParams();
        if (!is_array($params)) {
            $params = [];
        }

        foreach ($params as $name => $props) {
            // Continue the script

            $datatype = self::passDatatype($props);
            $modifier = self::passModifier($props);
            $ai = self::passAi($props);
            $attributes = self::passAttributes($props);
            $null = self::passNullable($props);
            $default = self::passDefault($props);
            $position = self::passPosition($props);
            self::passPrimary($props);
            $columns[] = trim("$modifier $datatype $attributes $null $default $ai $position");
        }

        self::passfk();
        self::passIndexes();
        self::passUniques();

        self::$query["datatypes"] = $columns;

        $allParts = array_merge(
            self::$query["fkDrop"] ?? [],
            self::$query['datatypes'] ?? [],
            self::$query['primary'] ?? [],
            self::$query['fk'] ?? [],
            self::$query['indexes'] ?? [],
            self::$query['uniques'] ?? []
        );
        return implode(", ", array_filter($allParts));
    }


    public function build()
    {
        return self::processParams();
    }
}
