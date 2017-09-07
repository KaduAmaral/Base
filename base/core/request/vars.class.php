<?php
namespace Core\Request;

class Vars {
   
   private $_data = [];

   public function __get($key) {
      if (isset($this->_data[$key]))
        return $this->_data[$key];

      throw new \Core\Exception\InvalidPropertyException("A propriedade '{$key}' é inválida.");
   }

   public function __set($key, $val) {
      $r = $this->_data[$key] = $val;
      $this->onSet($key, $val);
      return $r;
   }

   public function __isset($key) {
      return isset($this->_data[$key]);
   }

   public function onSet($key, $val) {}

}
