<?php
namespace Core;

require_once ADDONS.'ConnectionPDO'.DS.'ConnectionPDO.php';

use \ConnectionPDO;

use \Core\Model;

/**
* Connection
*/
class Connection extends ConnectionPDO {
   
   function __construct($settings) {
      $dns = $settings->driver . 
            ':host=' . $settings->host . 
            ';port=' . $settings->port . 
            ';dbname=' . $settings->schema;
      parent::__construct($dns, $settings->user, $settings->pass);
   }

   public function save(Model &$model){

      $reference = $model->getReference();
      $pk = $model->getPK();

      $data = get_object_vars($model);

      if (empty($pk))
         throw new \Exception('Model PK is empty!', 54);
         
      $pkv = $data[$pk];

      unset($data[$pk]);



      if (empty($pkv)) {
         $res = $this->insert($reference, $data)->execute();

         if ($res)
            $model->$pk = $this->lastInsertId();

         return $res;
      }
      else {
         foreach ($data as $col => $value)
            if (empty($value))
               unset($data[$col]);

         return $this->update($reference, $data, [$pk => $pkv])->execute();
      }

   }
} 