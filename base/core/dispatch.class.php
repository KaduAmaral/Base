<?php
namespace Core;

use \Core\Request;
use \Core\Routes\Router;

/**
* 
*/

class Dispatch {

   public function __invoke(Route $route = NULL) {
      $router = Router::getInstance();

      if (is_null($route))
         $route = $router->GetByRequest();

      return call_user_func_array([$route->getController(), $route->action], $route->attributes);
   }

}