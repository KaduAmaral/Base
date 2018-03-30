<?php
namespace Core;

use \Core\Routes\Router;
use \Core\Exception\InvalidApplicationException;
/**
* Roda o inicio da Application
* Com base na URL o Objeto Request avalia qual é a classe e o método a ser executado
*/

class Application {

   private static $config;

   public static function RUN($application) {

      try {
         self::$config = Config::SetApplication($application);

         Dispatch::initialize();

         self::loadConfigs();

      } catch (InvalidApplicationException $e) {
         echo self::Error($e->getMessage());
         return FALSE;
      }

      try {
         Dispatch::fire();
         
         
      } catch (\Exception $e) {
         echo self::Error($e->getMessage());
         return FALSE;
      }

      return TRUE;
   }
   
   private static function loadConfigs() {
      $configFiles = ['constants.php', 'config.php', 'routes.php', 'routes.cache.php'];

      foreach ($configFiles as $configFile) {
         self::loadConfigFile($configFile);
      }

   }

   private static function loadConfigFile($file) {
      if (is_readable(self::$config->dir . $file))
         require_once self::$config->dir . $file;
   }


   private static function Error($message = NULL) {
      if (class_exists('\\Controller\\ErrorController')) {
         $router = Router::getInstance();

         $app = new \Controller\ErrorController(Request::getInstance(), $router->notfound());
         return $app->index($message);
      } else {
         return 'Error: '.$message;
      }
   }

} 