<?php
namespace Core;

use \Core\Request;
use \Core\Config;
use \Core\Routes\Router;
use \Core\Exception\InvalidApplicationException;
/**
* Roda o inicio da Application
* Com base na URL o Objeto Request avalia qual é a classe e o método a ser executado
*/

class Application {

   public static function RUN($application) {

      try {
         $config = Config::SetApplication($application);

         if (is_readable($config->dir . 'config.php'))
            require_once $config->dir . 'config.php';

      } catch (InvalidApplicationException $e) {
         echo self::Error($e->getMessage());
         return FALSE;
      }

      $request = Request::getInstance();

      // Router em implementação
      $router = Router::getInstance();

      $route = $router->GetByRequest();

      if ($route) {
         $request->controller = $route->controller;
         $request->action = $route->action;
         $request->params = (object) $route->attributes;
      } else {
         $request->parseRoute();
      }

      $class = "\\Controller\\{$request->controller}Controller";

      // Retorno caso configuração $outputreturn do controller seja true

      $output = '';

      try {

         if (!class_exists($class) )
            throw new Exception("A URL {$request->uri} é inválida.");

         $app = New $class();

      } catch (Exception $e) {
         echo self::Error( $e->getMessage() );
         return FALSE;
      }


      if ( !empty($request->post['mvc:model']) ) {

         $model = '\Model\\' . array_remove($request->post, 'mvc:model') . 'Model';

         try {
            $param = New $model($request->post);
         } catch (Exception $e) {
            $app->setOutput($app->index());
            $app->output();
            return FALSE;
         }
      } else if (!!$route && count($route->attributes) > 0)
         $param = $route->attributes;
      else if ( empty($request->lost) && !is_numeric($request->lost) )
         $param = NULL;
      else
         $param = [$request->lost];


      try {
         $output = $app->execute( $param );
      } catch (Exception $e) {
         echo self::Error($e->getMessage());
         return FALSE;
      }

      if ($app->outputreturn)
         $app->setOutput( $output );

      $app->output();

      return TRUE;
   }


   private static function Error($message = NULL) {
      if (class_exists('\\Controller\\ErrorController')) {
         $app = New \Controller\ErrorController(Request::getInstance());
         return $app->index($message);
      } else {
         return 'Error: '.$message;
      }
   }

} 