<?php
namespace Core;

use \Core\Connection;
/**
* Model
*/
class Model {

   public static $connection;
   public static $controller;
   private $__error = NULL;


   function __construct($data = []) {
      if (!empty($data)){

         foreach ($data as $field => $value){
            if (method_exists($this, 'set'.ucfirst(strtolower($field))))
               $this->{'set'.$field}($value);
            elseif (property_exists(get_called_class(), $field)) {
               $this->{$field} = $value;
            }
         }
      }
   }

   public static function setConnection(Connection $connection) {
      self::$connection = $connection;
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

   public function _getError(){
      return $this->__error;
   }

   protected function _setError($error){
      $this->__error = $error;
      return $this->error;
   }

   protected function preSave() {
      return TRUE;
   }

   protected function afterSave($res) {
      return $res;
   }

   public function save(){

      if (!$this->preSave()) {
         return FALSE;
      }


      $reference = $this->getReference();
      $pk = $this->getPK();

      $data = get_object_vars($this);

      if (empty($pk)) {
         throw new \Exception('Model PK is empty!');
      }
         
      $pkv = !empty($data[$pk]) ? $data[$pk] : NULL;

      unset($data[$pk]);
      unset($data['__error']);

      if (empty($pkv)) {
         if (array_key_exists('created', $data))
            $data['created'] = Date('Y-m-d H:i:s');

         self::$connection->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );
         $res = self::$connection->insert($reference, $data)->execute();

         if ($res) {
            $this->{'set'.$pk}(self::$connection->lastInsertId());
         }


         $this->afterSave($res);

         return $res;
      } else {

         if (array_key_exists('modified', $data))
            $data['modified'] = Date('Y-m-d H:i:s');

         foreach ($data as $col => $value)
            if (empty($value) && $value !== NULL && $value !== 0)
               unset($data[$col]);

         $res = self::$connection->update($reference, $data, [$pk => $pkv])->execute();

         $this->afterSave($res);

         return $res;
      }

   }

} 