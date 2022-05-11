<?php
namespace App\Enums\Involucrados; 

use App\Enums\Enum;

abstract class Rol extends Enum{
  const actor           = 'Actor';
  const formulador      = 'Formulador';
  const implementador   = 'Implementador';

  protected static function getClass(){
    return __CLASS__;
  }
}

