<?php
namespace App\Enums\Proyectos;

use App\Enums\Enum;

abstract class Modalidad extends Enum{
  const hidrica       = 'Calidad y regulación hídrica';
  const conservacion  = 'Conservación de la biodiversidad';
  const culturales    = 'Culturales, espirituales y de recreación';
  const gases         = 'Reducción y captura de gases efecto invernadero';

  protected static function getClass(){
    return __CLASS__;
  }
}