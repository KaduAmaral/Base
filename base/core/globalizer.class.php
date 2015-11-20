<?php
namespace Core;
/**
* Globalizer
*/
class Globalizer
{
   private $data;
   private $lang;
   function __construct($lang = NULL) {
      if (!is_null($lang)) $this->lang = $lang;
   }

   public function getQuery($path, $lang = NULL){
      

   }
} 