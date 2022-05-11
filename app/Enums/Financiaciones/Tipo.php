<?php
namespace App\Enums\Financiaciones; 

use App\Enums\Enum;

abstract class Tipo extends Enum{
  const cooperacion_internacional = 'Cooperación internacional';
  const local                     = 'Local';
  const nacional                  = 'Nacional';
  const privada                   = 'Privada';
  const regional                  = 'Regional';
    
  protected static function getClass(){
    return __CLASS__;
  }
}

