<?php

   /**
    * Separador de diretório: /
    * 
    * @const string(1)
    */
   if (!defined('DS'))
      define('DS', '/');

   /**
    * Diretório ROOT: /
    * 
    * @const string
    */
   if ( !defined('ROOT') )
      define('ROOT', dirname( dirname(__DIR__) ) . DS );

   /**
    * Diretório do framework: /base/
    * 
    * @const string
    */
   if ( !defined('BASE') )
      define('BASE', ROOT . 'base' . DS  );

   /**
    * Diretório das aplicações: /apps/
    * 
    * @const string
    */
   if (!defined('APPS'))
      define('APPS', ROOT.'app'.DS);

   /**
    * Diretório do núcleo do Framework: /base/core/
    * 
    * @const string
    */
   if ( !defined('CORE') )
      define('CORE', BASE.'core'.DS);

   /**
    * Diretório dos arquivos de configurações: /base/config/
    * 
    * @const string
    */
   if ( !defined('CONFIG') )
      define('CONFIG',  __DIR__.DS);

   /**
    * @deprecated
    * 
    * Diretório de cache: /data/
    * 
    * @const string
    */
   if ( !defined('DATA') )
      define('DATA', ROOT.'data'.DS);

   /**
    * Extensões das classes: MinhaClasse.class.php
    * 
    * @const string
    */
   if ( !defined('CLASSEXTENSION') )
      define('CLASSEXTENSION', '.class.php');

   /**
    * Extensões dos arquivos de views: index.phtml
    * 
    * @const string
    */
   if ( !defined('VIEWEXTENSION') )
      define('VIEWEXTENSION', '.phtml');

   /**
    * Sal de segurança: HASH
    * 
    * @const string(32)
    */
   if ( !defined('SALT') )
      define('SALT', 'eb49b3a7147d35f491ee73318d888b0e');

   /**
    * Idioma padrão: pt-br
    * 
    * @const string(5){xx-xx}
    */
   if ( !defined('LANG') )
      define('LANG', 'pt-br'); 