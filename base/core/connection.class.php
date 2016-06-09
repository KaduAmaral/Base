<?php
namespace Core;

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
            ';dbname=' . $settings->schema . 
            ';charset='.(!empty($settings->charset) ? $settings->charset : 'utf8');

      parent::__construct($dns, $settings->user, $settings->pass);

      if (defined('DEBUG') && DEBUG === TRUE) 
         $this->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
   }

}