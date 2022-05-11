<?php
namespace App\Enums;

use ReflectionClass;

abstract class Enum{
    
  // Make sure there are never any instances created
  final private function __construct(){
    throw new Exception('Enum and Subclasses cannot be instantiated.');
  }

  final public static function getConstants(){
    $oClass = new ReflectionClass(static::getClass());
    return $oClass->getConstants();
  }

  final public static function getEnumOptions(){
    return array_values(self::getConstants());
  }
}

