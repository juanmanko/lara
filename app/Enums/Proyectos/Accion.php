<?php
namespace App\Enums\Proyectos; 

use App\Enums\Enum;

abstract class Accion extends Enum{
  const preservacion = 'Acciones de preservación';
  const restauracion = 'Acciones de restauración';

  protected static function getClass(){
    return __CLASS__;
  }
}


