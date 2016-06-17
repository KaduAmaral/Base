<?php

   /**
    * Separador de diretório: /
    * 
    * @const string(1)
    */
   if(!defined('DS')) 
      define('DS', '/'); // DIRECTORY_SEPARATOR

   /**
    * Repositório de funções
    */
   require_once __DIR__.DS.'repository.php';

   /**
    * Constantes padrões do sistema
    */
   require_once __DIR__.DS.'constants.php';

   /**
    * Auto Load do framework 
    */
   require_once __DIR__.DS.'autoload.php';

   /**
    * Auto Load de pacotes
    */
   if (is_readable(ROOT . DS . 'vendor' . DS . 'autoload.php'))
      include ROOT . DS . 'vendor' . DS . 'autoload.php';

   //require_once __DIR__.DS.'config.php'; 