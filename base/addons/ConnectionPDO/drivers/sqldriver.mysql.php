<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'driverinterface.php';

/**
* 
*/
class SQLDriver implements DriverInterface {

   private $params;
   private $log = '';

   public function select($table, $where = NULL, $cols = '*', $limit = NULL){

      $this->clearParams();

      $_where = !is_null($where) ? $this->where($where) : '';

      $_limit = (!is_null($limit) ? 'LIMIT '.$limit : '' );

      $sql = "SELECT {$cols} FROM `{$table}`{$_where}{$_limit};";

      $this->log($sql);

      return $sql;
   }


   public function insert($table, $data){

      $this->clearParams();

      $sql = "INSERT INTO `{$table}` ";

      $colunms = Array();

      $values  = Array();

      foreach ($data as $col => $value) {
         $colunms[] = "`{$col}`";
         $values[]  = '?';

         $this->addParam($col, $value);
      }

      $sql .= '(' . implode(', ', $colunms) . ') VALUES (' . implode(', ', $values) . ');';

      $this->log($sql);

      return $sql;
   }

   public function update($table, $data, $where = NULL){

      $this->clearParams();

      $sql = "UPDATE `{$table}` SET ";

      $values = Array();

      foreach ($data as $col => $value) {
         $values[]  = "`{$col}` = ?";
         $this->addParam($col, $value);
      }


      $sql .= implode(', ', $values);

      if (!is_null($where))
         $sql .= $this->where($where);

      $sql = $sql . ';';

      $this->log($sql);

      return $sql;

   }

   public function delete($table, $where = NULL){

      $this->clearParams();

      $sql = "DELETE FROM `{$table}`" . $this->where($where) . ';';

      $this->log($sql);

      return $sql;
   }

   public function drop($table){
      $sql = "DROP TABLE `{$table}`;";

      $this->log($sql);

      return $sql;
   }

   public function create($table, $fields, $options = NULL){

      $settings = array_merge(Array(
         'pk'        => NULL,
         'engine'    => 'MyISAM', 
         'charset'   => 'utf8', 
         'collate'   => 'utf8_bin'
      ), $options);

      if (is_null($table) || !is_string($table) || (is_string($table) && $table == '')) 
         throw new Exception('Error: First parameter `table name` is invalid for method `Create`!');

      if (!is_array($fields)) 
         throw new Exception('Error: Second parameter `table fields` is invalid for method `Create`!');

      $_pk =  !empty($settings['pk']) ? '  PRIMARY KEY (`' . (is_array($settings['pk']) ? implode('`, `', $settings['pk']) : $settings['pk']) .'`)' : '';

      $_fields = '';
      foreach ($fields as $field => $values) {
         $_fields .= '  ' . $this->createField($field, $values) . ',' . PHP_EOL;
      }

      $_fields = $_pk === '' ? rtrim($_fields, ',' . PHP_EOL) : $_fields . $_pk;

      $_enginne = '';
      if (is_string($settings['engine']) && !empty($settings['engine']))
         $_enginne = " ENGINE = {$settings['engine']}";

      $_charset = '';
      if (is_string($settings['charset']) && !empty($settings['charset']))
         $_charset = " DEFAULT CHARACTER SET = {$settings['charset']}";

      if (is_string($settings['collate']) && !empty($settings['collate']))
         $_collate = " COLLATE = {$settings['collate']}";

      $_sql = "CREATE TABLE IF NOT EXISTS `{$table}` (" . PHP_EOL . $_fields . PHP_EOL . ") {$_enginne}{$_charset}{$_collate};";

      $this->log($_sql);

      return $_sql;
   }

   private function createField($name, $settings){
      $field = "`{$name}` ";

      if (isset($settings['type'])) $field .= $settings['type'];
      if (isset($settings['size'])) $field .= ' ('.$settings['size'].')';
      if (isset($settings['pk']) && $settings['pk']) $field .= ' PRIMARY KEY';
      if (isset($settings['auto']) && $settings['auto']) $field .= ' AUTO_INCREMENT';
      if (isset($settings['null']) && $settings['null']) $field .= ' NULL '; else $field .= ' NOT NULL';
      if (isset($settings['deafult'])) $field .= ' DEFAULT '.(is_null($settings['deafult']) ? 'NULL' : "'{$settings['deafult']}'");
      if (isset($settings['comment'])) $field .= " COMMENT '{$settings['comment']}'";

      return $field;
   }

   public function setParams(PDOStatement &$stmt){
      $params = $this->getParams();
      $this->log('Setando Parâmetros: ');
      if (is_array($params) && !empty($params)){
         foreach ($params as $param => $value){
            $stmt->bindValue($param+1, $this->prepareParam($value), $this->getParamType($value));
            $this->log($param+1 . ' => ' . $this->prepareParam($value));
         }
      }
      $this->log(PHP_EOL.str_repeat('-', 80).PHP_EOL);
   }

   private function prepareParam($value){
      if (is_numeric($value) && is_float($value))
         return "{$value}";
      else
         return $value;
   }

   private function getParamType($value){
      if (is_null($value))
         return PDO::PARAM_NULL;
      else if (is_bool($value))
         return PDO::PARAM_BOOL;
      else if (is_numeric($value) && is_integer($value + 0))
         return PDO::PARAM_INT;
      else 
         return PDO::PARAM_STR;


   }

   private function where($where) {

      if (is_null($where)) return '';

      $_where = ' WHERE ';

      if (is_string($where) && $where != '') return $_where.$where;

      foreach ($where as $col => $value) {

         if ($value === 'OR'){
            $_where = substr($_where, 0, strlen($_where)-5).' OR ';
            continue;
         }

         $_where .= "`{$col}`";

         if (is_string($value) || is_numeric($value)){
            $_where .= " = ?";
            $this->addParam('onwhere'.$col, $value);
         } elseif (is_null($value)){
            $_where .= " IS NULL";
         } 
         elseif (is_array($value)) {
            if (array_key_exists('NOT', $value))
               $_where .= $value['NOT'] === NULL ? ' IS NOT NULL' : $this->prepareInWhere($col, $value);
            else if (array_key_exists('BETWEEN', $value)){
               if (count($value['BETWEEN']) !== 2) 
                  throw new Exception('Error: `where` clause BETWEEN data size is invalid!');

               $this->addParam('onwherebetween0'.$col, $value['BETWEEN'][0]);
               $this->addParam('onwherebetween1'.$col, $value['BETWEEN'][1]);

               $_where .= " BETWEEN ? AND ?";
            } else if (array_key_exists('LIKE', $value)){
               $this->addParam('onwherelike'.$col, '%'.$value['LIKE'].'%');
               $_where .= " LIKE ?";
            } else { // IN
               $_where .= $this->prepareInWhere($col, $value);
            }
         }
         $_where = rtrim($_where).' AND ';
      }
      return substr($_where, 0, strlen($_where)-5);
   }

   private function prepareInWhere($col, $in){

      $where = '';
      $not = '';
      if (array_key_exists('NOT', $in)) {
         $in = $in['NOT'];
         $not = 'not';
         $where .= ' NOT';
      }

      $paramkey = "onwhere{$col}{$not}in";

      $where .= " IN (";

      $value = '';

      


      $counter = 0;
      if (in_array('>>>', $in)){
         for ($i=$in[0]; $i <= $in[2]; $i++){
            if (isset($in[3]) && in_array($i, $in[3])) 
               continue;
            $where .= '?, ';//':'.$paramkey.$counter.', ';
            $this->addParam($paramkey.$counter, $i);
            $counter++;
         }
      } else {
         foreach ($in as $vals){
            $where .= '?, '; //':'.$paramkey.$counter.', ';
            $this->addParam($paramkey.$counter, $vals);
            $counter++;
         }
      }

      $where = substr($where, 0, strlen($where) -2) . ')';

      return $where;
   }

   public function getParam($key){
      return $this->params[$key];
   }

   public function getParams(){
      return $this->params;
   }

   public function addParam($key, $value){
      //$this->params[':'.$key] = $value;
      $this->params[] = $value;
   }

   public function clearParams(){
      $this->params = Array();
   }

   public function log($str){
      $this->log .= $str . PHP_EOL;
   }

   public function flushLog(){
      $log = $this->log;
      $this->log = '';
      return $log;
   }



} 