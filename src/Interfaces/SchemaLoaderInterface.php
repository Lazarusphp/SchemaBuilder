<?php
namespace LazarusPhp\SchemaBuilder\Interfaces;

interface SchemaLoaderInterface
{

    public function up($schema):void;
    public function down():void;

}