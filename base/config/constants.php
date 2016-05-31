<?php

   define('VERSION', '0.0.0-DEV.2');

   //             root     base    config
   define('ROOT', dirname( dirname(__DIR__) ) . DS );

   define('BASE', ROOT . 'base' . DS  );

   define('APPS', ROOT.'app'.DS);

   define('CORE', BASE.'core'.DS);

   define('CONFIG',  __DIR__.DS);

   define('DATA', ROOT.'data'.DS);

   define('CLASSEXTENSION', '.class.php');

   define('VIEWEXTENSION', '.phtml');

   // Security SALT
   define('SALT', 'eb49b3a7147d35f491ee73318d888b0e');

   // Default language
   define('LANG', 'pt-br'); 