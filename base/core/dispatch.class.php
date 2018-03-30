<?php
namespace Core;

use Core\Exception\InvalidPropertyException;
use core\routes\Annotation;
use \Core\Routes\Router;
use \Core\Routes\Route;


class Dispatch {

   private static $request;
   private static $config;
   private static $router;
   private static $annotation;
   private static $route;
   private static $app;
   private static $params = [];
   private static $output = '';

   public static function fire() {
      try {
         self::findRoute();
         self::prepare();
         self::execute();
         self::rprint();
      } catch (\Exception $e) {
         throw $e;
      }
   }

   private static function prepare() {
      $class = self::$route->getController();

      if (!class_exists($class) )
         throw new Exception('A URL '.self::$request->uri.' é inválida.');

      self::$app = New $class(self::$request, self::$route);

      if (!!self::$route && count(self::$route->attributes) > 0) {
         $ref = new \ReflectionMethod(self::$app, self::$route->action);

         $parameters = $ref->getParameters();

         foreach ($parameters as $parameter) {

            if (!isset(self::$route->attributes[$parameter->getName()])) {

               if (!$parameter->isOptional())
                  throw new InvalidPropertyException('A propriedade "'.$parameter->getName().'" não está declarada na rota.');

               self::$params[$parameter->getPosition()] = $parameter->getDefaultValue();
               continue;
            }


            self::$params[$parameter->getPosition()] = self::$route->attributes[$parameter->getName()];

         }
      }
   }

   private static function execute() {
      try {
         self::$output = call_user_func_array([self::$app, self::$route->action], self::$params);
      } catch (\Exception $e) {
         throw $e;
      }
   }

   private static function rprint() {
      if (self::$app->outputreturn)
         self::$app->setOutput( self::$output );

      self::$app->output();
   }



   public static function initialize() {
      self::$request = Request::getInstance();
      self::$config = Config::getInstance();
      self::$router = Router::getInstance();
      try {
         self::$annotation = new Annotation();
      } catch (\Exception $e) {
         throw $e;
      }


   }

   private static function findRoute() {
      self::$route = self::$router->match();
      if (self::$config->onlyroutes && !self::$route) {
         self::$route = Router::notfound();
      }

      if (!self::$route)
         self::$route = self::$request->getRouteByRequest();
   }


}