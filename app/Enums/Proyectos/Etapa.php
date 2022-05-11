<?php
namespace App\Enums\Proyectos;

use App\Enums\Enum;

abstract class Etapa extends Enum{
  const inversion     = 'Inversión y Operación (Implementación)';
  const preinversion  = 'Preinversión (Diseño)';

  protected static function getClass(){
    return __CLASS__;
  }
}