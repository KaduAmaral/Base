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
      if (!empty($data)) $this->_setData($data);
   }

   public function refresh() {
      $data = self::getWhere($this->_getPKValue(), TRUE, FALSE);
      if (!empty($data) && is_array($data)) {
         $this->_setData($data);
         return TRUE;
      }
      
      return FALSE;
      
   }

   private function _setData($data) {
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

   /**
    * Set the static method with current connection
    * @param Connection $connection 
    * @return void
    */
   public static function _setConnection(Connection $connection) {
      self::$connection = $connection;
   }

   /**
    * Get the model reference (table name on database)
    * @return string
    */
   public static function _getReference(){
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

   public static function _isPkAi() {
      return (property_exists(get_called_class(), '_ispkai') ? static::$_ispkai : TRUE);
   }

   /**
    * Get model Primary Key field
    * @return string/array
    */
   public static function _getPK(){
      return (property_exists(get_called_class(), '_pk') ? static::$_pk : 'id');
   }

   /**
    * Get PK(s) value
    * @return array ('pk' => 'value')
    */
   public function _getPKValue() {
      $pks = self::_getPK();

      if (!is_array($pks))
         $pks = [$pks];

      $pksv = [];
      foreach ($pks as $pk) {
         $pksv[$pk] = $this->{'get'.ucfirst(strtolower($pk))}();
      }

      return $pksv;
   }

   /**
    * Get register by ID
    * @param string/int $id 
    * @return Model
    */
   public static function getById($id) {
      return self::getByColunm(self::_getPK(), $id);
   }

   /**
    * Get register by colunm
    * @param string $colunm 
    * @param string/int $value 
    * @return Model
    */
   public static function getByColunm($colunm, $value) {
      return self::getWhere([$colunm => $value]);
   }

   /**
    * Get All registers 
    * @return Array(Model)
    */
   public static function getAll() {
      return self::getWhere(NULL);
   }

   /**
    * Get registers 
    * @param array/string $where (i.e: ['colunm'=>'value', 'colunm2' => 'value2'] || "colunm = 'value' AND colunm2 = 'value2") 
    * @return Array(Model)
    */
   public static function getWhere($where, $shift = TRUE, $setobj = TRUE) {

      $stmt = self::$connection->select( self::_getReference(), $where );
      $res = $stmt->execute();

      $rows = array();
      $pk = self::_getPK();
      while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
         if (is_array($pk))
            $rows[] = ($setobj ? new static($row) : $row);
         else 
            $rows[ $row[ $pk ] ] = ($setobj ? new static($row) : $row);
      }

      if (count($rows) == 0)
         return NULL;

      return ((count($rows) == 1 && $shift) ? array_shift($rows) : $rows);
   }

   /**
    * Get some error
    * @return string
    */
   public function _getError(){
      return $this->__error;
   }

   /**
    * Set some error
    * @param string $error 
    * @return string
    */
   protected function _setError($error){
      $this->__error = $error;
      return $this->__error;
   }

   /**
    * Delete By ID
    * @param int/string $id PK
    * @return bool
    */
   public static function deleteById($id) {
      return self::deleteWhere([self::_getPK() => $id]);
   }

   /**
    * Delete By ID
    * @param int/string $id PK
    * @return bool
    */
   public static function deleteWhere($where) {
      $pk = self::_getPK();
      $reference = self::_getReference();

      return self::$connection->delete($reference, $where)->execute();
   }

   /**
    * PreSave validation function (to override)
    * @return bool
    */
   protected function preSave() {
      return TRUE;
   }


   /**
    * After Save actions function (to override)
    * @param bool $res Resulto of save
    * @return void
    */
   protected function afterSave($res) {
      return;
   }

   /**
    * Save method, save current Model (Insert or Update)
    * @return bool
    */
   public function save(){

      if (!$this->preSave()) {
         return FALSE;
      }

      $reference = $this->_getReference();
      $pk = $this->_getPK();

      $data = get_object_vars($this);

      if (empty($pk) && $pk !== NULL) {
         throw new \Exception('Model PK is empty!');
      }

      if (is_array($pk)) {
         $pkv = [];
         $_pkv = [];
         $insert = TRUE;
         foreach ($pk as $c) {
            if (!empty($data[$c])) {
               $pkv[] = $data[$c];
               $_pkv[$c] = $data[$c];
               // unset($data[$c]);
               $insert = FALSE;
            }
         }

         if (!$insert) {
            $insert = is_null(self::getWhere($_pkv));
         }


      } else {
         $pkv = !empty($data[$pk]) ? $data[$pk] : NULL;
         $_pkv = [$pk => $pkv];

         // Se for AUTO INCREMENT remove dos campos a serem INSERIDOS/ALTERADOS
         if (self::_isPkAi())
            unset($data[$pk]);

         $insert = empty($pkv);
      }

      unset($data['__error']);

      foreach ($data as $key => $value) {
         if (strpos($key, '_') === 0)
            unset($data[$key]);
      }

      define('DEBUG', TRUE);

      if ($insert) {


         if (array_key_exists('created', $data))
            $data['created'] = Date('Y-m-d H:i:s');

          if (array_key_exists('modified', $data))
            unset($data['modified']);


         if (defined('DEBUG') && DEBUG === TRUE) 
            self::$connection->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );

         $res = self::$connection->insert($reference, $data)->execute();

         if ($res && $pk !== NULL && !is_array($pk)) {
            $this->{'set'.$pk}(self::$connection->lastInsertId());
         }


         $this->afterSave($res);

         return $res;
      } else {

         if (array_key_exists('modified', $data))
            $data['modified'] = Date('Y-m-d H:i:s');

         if (array_key_exists('created', $data))
            unset($data['created']);

         foreach ($data as $col => $value)
            if (empty($value) && $value !== NULL && $value !== 0)
               unset($data[$col]);

         if (defined('DEBUG') && DEBUG === TRUE) self::$connection->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );
         $res = self::$connection->update($reference, $data, $_pkv)->execute();

         $this->afterSave($res);

         return $res;
      }

   }

} 