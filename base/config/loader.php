<?php

   if(!defined('DS')) define('DS', '/'); // DIRECTORY_SEPARATOR

   require_once __DIR__.DS.'repository.php';

   require_once __DIR__.DS.'constants.php';

   require_once __DIR__.DS.'autoload.php';

   if (is_readable(ROOT . DS . 'vendor' . DS . 'autoload.php'))
      require_once ROOT . DS . 'vendor' . DS . 'autoload.php';

   //require_once __DIR__.DS.'config.php'; 