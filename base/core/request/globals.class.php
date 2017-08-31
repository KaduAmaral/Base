<?php

namespace Core\Request;

class Globals extends Vars {

   public function __construct() {
      foreach ($GLOBALS as $key => $val) {
         $this->__set($key, $val);
      }
   }

   public function onSet($key, $val) {
      $GLOBALS[$key] = $val;
   }

}
