<?php
namespace App\Enums\Historicos; 

use App\Enums\Enum;

abstract class Estado extends Enum{
  const aprobado    = 'Aprobado';
  const borrador    = 'Borrador';
  const eliminado   = 'Eliminado';
  const rechazado   = 'Rechazado';
  const registrado  = 'Registrado';
  const revisado    = 'Revisado';
  
  protected static function getClass(){
    return __CLASS__;
  }
}

