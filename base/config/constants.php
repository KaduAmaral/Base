<?php

   define('VERSION', '0.0.0-DEV.2');

   if (!defined('PROTOCOL'))
      define('PROTOCOL', getProtocol());

   if (!defined('URL'))
      define('URL', PROTOCOL.$_SERVER['HTTP_HOST']);

   //             root     base    config
   define('ROOT', dirname( dirname(__DIR__) ) . DS );

   define('BASE', ROOT . 'base' . DS  );

   define('APPS', ROOT.'app'.DS);

   define('PUBLIC', ROOT.'public_html'.DS);

   define('CORE', BASE.'core'.DS);

   define('CONFIG',  __DIR__.DS);

   define('ADDONS',  BASE.'addons'.DS);

   define('DATA', ROOT.'data'.DS);

   define('CLASSEXTENSION', '.class.php');

   define('VIEWEXTENSION', '.phtml');

   // Security SALT
   define('SALT', '35f491eb0eeb49b3a714e73318d8887d');

   // Default language
   define('LANG', 'pt-br'); 