<?php

namespace Core\Routes;

use \Core\Request;

class Matcher {

   public $matchedRoute;

   public $failedRoute;

   public $failedScore;

   public $log;


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

      $this->matchedRoute = false;
      $this->failedRoute = null;
      $this->failedScore = 0;
      $this->log = '';

      foreach ($routes as $name => $proto) {
         $route = $this->requestRoute($request, $proto, $name, $request->uri);
         
         if ($route)
            return $route;
      }

      return NULL;
   }

   public function requestRoute(Request $request, Route $proto, $name, $path) {
      $route = clone $proto;
      return $this->applyRules($request, $route, $name, $path);
   }

   public function applyRules(Request $request, Route $route, $name, $path) {
      $score = 0;
      
      foreach ($route->rules as $rule) {
         if (!$rule($request, $route)) {
            return $this->ruleFailed($request, $route, $name, $path, $rule, $score);
         }

         $score++;
      }

      return $this->routeMatched($route, $name, $path);
   }


   public function routeMatched(Route $route, $name, $path) {
      $this->log .= "{$path} MATCHED ON {$name}" . PHP_EOL;
      $this->matchedRoute = $route;
      return $route;
   }

   protected function ruleFailed($request, $route, $name, $path, $rule, $score) {

      $ruleClass = get_class($rule);
        
      if (!$this->failedRoute || $score > $this->failedScore) {
         $this->failedRoute = $route;
         $this->failedScore = $score;
      }

      $this->log .= "{$path} FAILED {$ruleClass} ON {$name}" . PHP_EOL;

      return FALSE;
    }
}