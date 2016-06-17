<?php
namespace Core\Exception;

use \ArrayIterator;

/**
* Exceptios List
*/
class ExceptionsList extends ArrayIterator {

   function __construct() {
      parent::__construct([
         'Exception'                => ['code' => 0,     'message' => "%s"],
         'SystemException'          => ['code' => 1,     'message' => "%s"],
         'ClassNotFoundException'   => ['code' => 52,    'message' => "A classe '%s' não foi encontrada."],
         'FileNotFoundException'    => ['code' => 404,   'message' => "O arquivo '%s' não existe."],
         'InvalidArgumentException' => ['code' => 534,   'message' => "O parâmetro '%s' é inválido."]
      ]);
   }

   public function exception($name, $args = NULL) {
      return new "\\Core\Exception\\{$name}"
   }

} 