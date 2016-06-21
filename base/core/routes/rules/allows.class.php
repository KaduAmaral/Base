<?php

namespace Core\Routes\Rules;

use \Core\Request;
use \Core\Routes\Route;

class Allows implements RuleInterface {
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
   public function __invoke(Request $request, Route $route) {
      if (!$route->allows) {
         return TRUE;
      }

      $requestMethod = $request->getMethod() ?: 'GET';
      return in_array($requestMethod, $route->allows);
   }
}