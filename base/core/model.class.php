<?php
namespace Core;
/**
* Model
*/
class Model {

   public static $connection;

   function __construct($data = []) {
      if (!empty($data))
         foreach ($data as $field => $value)
            $this->$field = $value;
   }

   public static function getReference(){
      if (property_exists(get_called_class(), '_reference'))
         $reference = static::$_reference;
      else {
         $class = strtolower(get_called_class());
         $class = explode('\\', $class);
         $class = end($class);
         $reference = substr($class, 0, strlen($class) - strlen('model'));
      }

      return $reference;
   }

   public static function getPK(){
      return (property_exists(get_called_class(), '_pk') ? static::$_pk : 'id');
   }

   public static function getById($id) {
      return self::getByColunm(self::getPK(), $id);
   }

   public static function getByColunm($colunm, $value) {
      return self::getWhere([$colunm => $value]);
   }




   public static function getAll() {
      return self::getWhere(NULL);
   }

   public static function getWhere($where) {
      $stmt = self::$connection->select( self::getReference(), $where );
      $res = $stmt->execute();

      $rows = array();

      while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
         $rows[ $row[ self::getPK() ] ] = new static($row);
      }

      if (count($rows) == 0)
         return NULL;

      return (count($rows) == 1 ? array_shift($rows) : $rows);
   }

} 