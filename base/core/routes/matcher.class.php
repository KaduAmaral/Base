<?php

namespace Core\Routes;

use \Core\Request;

class Matcher {

   /**
    *
    * Verifica se a requisição é compatível com a rota.
    *
    * @param Request $request Requisição HTTP.
    *
    * @param Route $route Rota.
    *
    * @return bool TRUE quando sucesso, FALSE quando falhar.
    *
    */
   public function __invoke(Request $request, Routes $routes) {
      var_dump($routes);
      foreach ($routes as $name => $proto) {
         $route = $this->requestRoute($request, $proto, $name, $request->uri);

         if ($route) {
             return $route;
         }
      }

      return NULL;
   }

   public function requestRoute(Request $request, Route $proto, $name, $path) {
      echo $request->uri . PHP_EOL;
      echo $request->route . PHP_EOL;
      echo $request->pattern . PHP_EOL;
      


      return FALSE;
   }

   public function applyRules(Request $request, Route $route) {
      if ($route->rules)
         foreach ($route->rules as $rule) 
            if (!$rule($request, $route))
               return FALSE;

   }
}