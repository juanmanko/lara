<?php
namespace App\Enums\Proyectos;

use App\Enums\Enum;

abstract class Metodo extends Enum{
  const beneficio_neto  = 'Beneficio económico neto';
  const valor_renta     = 'Valor de la renta de la tierra';
  const otro            = 'Otro';
  

  protected static function getClass(){
    return __CLASS__;
  }
}