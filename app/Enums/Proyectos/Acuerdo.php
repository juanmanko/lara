<?php
namespace App\Enums\Proyectos;

use App\Enums\Enum;

abstract class Acuerdo extends Enum{
  const individual   = 'Individual';
  const colectivo    = 'Colectivo';

  protected static function getClass(){
    return __CLASS__;
  }
}