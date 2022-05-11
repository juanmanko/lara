<?php
namespace App\Enums\Proyectos;

use App\Enums\Enum;

abstract class Shape extends Enum{
  const catastro = 'Catastro';
  const dibujado = 'Dibujado sobre imagen satelital';
  const gps = 'Levantamiento con navegador (GPS)';
  const hiposometrico = 'Levantamiento hiposométrico';
 

  protected static function getClass(){
    return __CLASS__;
  }
}