<?php
namespace Core\Exception;

use \Exception;

/**
* Throws - Listagem de Erros
*/
class SystemException extends Exception {

   function __construct($code, $args = array(), Exception $previous = NULL ) {

      $language = (require BASE . 'language' . DS . LANG . DS . 'exceptions.php');
      $format = $language[$code];
      
      $message = vsprintf($format, $args);

      parent::__construct($message, $code, $previous);
   }

   // personaliza a apresentação do objeto como string
   // public function __toString() {
   //    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
   // }
} 