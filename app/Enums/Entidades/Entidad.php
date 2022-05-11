<?php
namespace App\Enums\Entidades; 

use App\Enums\Enum;

abstract class Entidad extends Enum{
  const cooperacion   = 'Cooperación internacional';
  const comunitarias  = 'Organizaciones comunitarias';
  const privada       = 'Privada';
  const publica       = 'Pública';

  protected static function getClass(){
    return __CLASS__;
  }
}

