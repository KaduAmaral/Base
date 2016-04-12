<?php
namespace Core;
use \Core\Exception\SystemException;
use \Core\Exception\Exceptions;
/**
* 
*/
class Route {

   private static $routes = [];

   
   public static function guest() {
      if (file_exists(APPS.APP.DS.'routes.php')){
         $routes = (require APPS.APP.DS.'routes.php');
         foreach ($routes as $url => $params)
            self::register($params, $url);
      }
   }

   /**
    * Registro de rotas
    * Exemplo: Array(
    *             'controller' => 'main',    // REQUIRED
    *             'action'     => 'index'    // REQUIRED
    *             'lang'       => 'pt-br'    // OPTIONAL
    *             'permission' => TRUE/FALSE // OPTIONAL
    *          )
    */

   public static function register($params, $url = NULL){

      if (empty($params['controller']))
         throw new SystemException(Exceptions::E_INVALIDPARAMETERVALUE, ['NULL', '$params[controller]']);

      if (empty($params['action']))
         throw new SystemException(Exceptions::E_INVALIDPARAMETERVALUE, ['NULL', '$params[action]']);

      if (empty($params['lang']))
         $params['lang'] = (empty($_COOKIE['language']) ? LANG : $_COOKIE['language']);

      if (is_null($url))
         $url = $params['controller'].'/'.$params['action'];

      self::$routes[$url] = $params;

   }

   public static function set($params){
      return self::href($params);
   }

   public static function href($params = ''){
      $uri = is_string($params) ? $params : self::_href($params);

      if (empty($uri))
         return rtrim(URL, '/');

      $url = rtrim(URL, '/') . '/' . ltrim($params, '/');

      return $url;
   }

   private static function _href($route){
      $uri = '';

      if (is_object($route)) {
        $uri  = property_exists($route, 'controller') ? $route->controller : 'index';
        $uri .= '/';
        $uri .= property_exists($route, 'action') ? $route->action : 'index';
      } else if (is_array($route)) {
        $uri  = !empty($route['controller']) ? $route['controller'] : 'index';
        $uri .= '/';
        $uri .= !empty($route['action']) ? $route['action'] : 'index';
      }


      if (!empty($route['params']))
         foreach ($route['params'] as $key => $val)
            $uri .= '/' . $key . '/' . urlencode($val);

      return ($uri === 'index/index') ? '' : $uri;
   }

} 