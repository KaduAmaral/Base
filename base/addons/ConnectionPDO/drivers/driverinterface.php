<?php
/**
* 
*/
interface DriverInterface {
   
   public function select($table, $where = NULL, $cols = '*', $limit = NULL);

   public function insert($table, $data);

   public function update($table, $data, $where = NULL);

   public function delete($table, $where = NULL);

   public function drop($table);

   public function create($table, $fields, $options = NULL);

} 