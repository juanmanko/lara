<?php
namespace App\Enums\Proyectos;

use App\Enums\Enum;

abstract class Periodicidad extends Enum{
  
  const mensual     = 'Mensual';
  const bimensual   = 'Bimensual';
  const trimestral  = 'Trimestral';
  const semestral   = 'Semestral';
  const anual       = 'Anual';
  const otra        = 'Otra';


  protected static function getClass(){
    return __CLASS__;
  }
}