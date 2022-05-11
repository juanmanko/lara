<?php
namespace App\Enums\Proyectos;

use App\Enums\Enum;

abstract class Pago extends Enum{
  const dinero    = 'Dinero';
  const especie   = 'Especie';

  protected static function getClass(){
    return __CLASS__;
  }
}