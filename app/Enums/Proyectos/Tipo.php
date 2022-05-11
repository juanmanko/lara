<?php
namespace App\Enums\Proyectos;

use App\Enums\Enum;

abstract class Tipo extends Enum{
  const voluntario    = 'Voluntario';
  const cumplimiento  = 'Cumplimiento de obligaciones ambientales';

  protected static function getClass(){
    return __CLASS__;
  }
}