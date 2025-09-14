<?php
namespace LazarusPhp\SchemaBuilder\Interfaces;

interface SchemaLoaderInterface
{

    public function up($schema):void;
    public function alter($schema):void;
    public function down($schema):void;

}