<?php
namespace App\Enums\Comentarios; 

use App\Enums\Enum;

abstract class Anexo extends Enum{
  const formulacion   = 'Documento de la formulación del proyecto';
  const info_predios  = 'Información de los predios seleccionados como: cédula catastral, folio matricula inmobiliaria, número de ecritura pública, entre otros';
  const otros         = 'Otros documentos de interés';
  const shapes        = 'Shapes de los predios del proyecto';
  
  protected static function getClass(){
    return __CLASS__; 
  }
}



