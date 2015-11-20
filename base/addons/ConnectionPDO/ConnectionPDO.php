<?php
/**
* ConnectionPDO - Classe de Conexão abstrata usando PDO do PHP
* 
*/
class ConnectionPDO extends \PDO {

   private $driver;
   private $stmt;
   private $lastSQL;
   private $log = '';

   function __construct($dsn, $username = NULL, $password = NULL, $options = NULL) {
      parent::__construct($dsn, $username, $password, $options);
      $this->LoadDriverMethods();
   }


      /**
    * @method  Select
    * @param   $table - Nome da tabela
    * @param   $where - Array de condições
    * @param   $cols  - Colunas para buscar (padrão * - todas)
    * @param   $limit - Limite de linhas
    * @return  Retorna PDOStatement em sucesso e FALSE quando erro
    */
   public function select($table, $where = NULL, $cols = '*', $limit = NULL) {
      $this->lastSQL =  $this->driver->select($table, $where, $cols, $limit);

      $this->stmt = $this->prepare($this->lastSQL);

      if (!is_null($where))
         $this->driver->setParams($this->stmt);

      //echo 'Parâmetros: ';
      //var_dump($this->driver->getParams());

      $this->log($this->driver->flushLog());

      return $this->stmt;
   }

      /**
    * @method  insert
    * @param   $table - Nome da tabela
    * @param   $data - Array de dados (coluna => valor)
    * @return  Retorna PDOStatement em sucesso e FALSE quando erro
    */
   public function insert($table, $data) {
      $this->lastSQL = $this->driver->insert($table, $data);

      $this->stmt = $this->prepare($this->lastSQL);

      $this->driver->setParams($this->stmt);

      $this->log($this->driver->flushLog());

      return $this->stmt;
   }

   /**
    * @method  update
    * @param   $table - Nome da tabela
    * @param   $data  - Array de dados a serem atualizados (coluna => valor)
    * @param   $where - Array com dados do WHERE (ver documentação para detalhes)
    * @return  Retorna PDOStatement em sucesso e FALSE quando erro
    */
   public function update($table, $data, $where = NULL) {
      $this->lastSQL = $this->driver->update($table, $data, $where);

      $this->stmt = $this->prepare($this->lastSQL);

      $this->driver->setParams($this->stmt);

      $this->log($this->driver->flushLog());

      return $this->stmt;
   }

   public function delete($table, $where = NULL){
      $this->lastSQL = $this->driver->delete($table, $where);

      if (!is_null($where))
         $this->stmt = $this->prepare($this->lastSQL);

      $this->driver->setParams($this->stmt);

      $this->log($this->driver->flushLog());

      return $this->stmt;
   }

   public function drop($table){
      $this->lastSQL = $this->driver->drop($table);

      $this->stmt = $this->prepare($this->lastSQL);

      $this->log($this->driver->flushLog());

      return $this->stmt;
   }

   public function create($table, $fields, $options = NULL){
      if(!empty($options) && is_array($options) && array_key_exists('drop', $options) && $options['drop']){
         $this->drop($table)->execute();
         unset($options['drop']);
      }

      $this->lastSQL = $this->driver->create($table, $fields, $options);

      $this->stmt = $this->prepare($this->lastSQL);

      $this->log($this->driver->flushLog());

      return $this->stmt;
   }

   public function getTables() {
      $query = $this->query('SHOW TABLES');
      return $query->fetchAll(PDO::FETCH_COLUMN);
   }

   public function lastSQL(){
      return $this->lastSQL;
   }

   private function LoadDriverMethods(){
      $driver = __DIR__ . DIRECTORY_SEPARATOR . 'drivers' . 
                          DIRECTORY_SEPARATOR . 'sqldriver.' . 
                          strtolower($this->getAttribute(PDO::ATTR_DRIVER_NAME)) . '.php';

      if (!is_file($driver))
         throw new Exception('Não foi possível carregar os métodos do driver', 1);

      require_once $driver;
      $this->driver = new SQLDriver();
   }

   private function log($str){
      $this->log .= $str . PHP_EOL;
   }

   public function flushLog(){
      $log = rtrim($this->log, '-'.PHP_EOL);
      $this->log = '';
      return $log;
   }

} 