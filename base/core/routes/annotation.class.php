<?php
namespace core\routes;


class Annotation {

   /**
    * @var \ReflectionClass
    */
   private $reflection;

   private $classRoute;

   private $actionsRoutes = [];

   public function __construct() {

   }

   public function reflect($className) {
      $this->reflection = new \ReflectionClass($className);
   }

   public function parse() {
      $this->classRoute = $this->parseDoc($this->reflection->getDocComment() );

      $methods = $this->reflection->getMethod(\ReflectionMethod::IS_PUBLIC);


      foreach ($methods as $method) {
         $route = $this->parseDoc($method->getDocComment());

         if ($route)
            $this->actionsRoutes[$method->getName()] = $route;
      }
   }

   public function parseDoc($docblock) {
      if (empty($docblock))
         return FALSE;

      $doc = trim($docblock, "\n*");
      $doc = explode("\n", $doc);
      foreach ($doc as $line) {
         $match = preg_match('/\@Route(\s+)?\(\"?([a-zA-Z\:\/]+)\"(.*)?\)/', trim($line, ' /*'), $matches );
         if (0 < $match)
            break;
      }

      if (is_null($matches))
         return FALSE;

      if (empty($matches[2]))
         return FALSE;

      $path = $matches[2];

      $params = NULL;
      if (!empty($matches[3]))
         $params = $this->parseParams($matches[3]);

      return [
         'path' => $path,
         'params' => $params
      ];

   }

   private function parseParams($string) {
      $string = trim($string, ' ,');

      $matches = NULL;
      $match = preg_match_all('/(\w+\=["{][a-zA-Z0-9_\-"\+\\\\\= ]+[}"])/', $string, $matches);

      $params = NULL;


      if (!$match)
         return FALSE;

      $params = $matches[0];

      $parameters = [];
      foreach ($params as $param) {
         $key = trim(substr($param, 0, strpos($param, '=') ));
         $value = trim(substr($param, strpos($param, '=') + 1), ' "');

         if (strpos($value, '{') === 0)
            $value = $this->parseParams( trim($value, ' {}') );

         $parameters[$key] = $value;

      }

      return $parameters;
   }

}