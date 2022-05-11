<?php
namespace App\Enums\Comentarios; 

use App\Enums\Enum;

abstract class Comentario extends Enum{
  const abierto   = 'Abierto';
  const atendido  = 'Atendido';
  const cerrado   = 'Cerrado';
  
  protected static function getClass(){
    return __CLASS__; 
  }
}



