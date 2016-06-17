<?php
namespace Core\Routes;

use \Core\Request;
use \Core\Config;
use \Core\Dispatch;

/**
* 
*/
class Router {

   private static $instance;

   /**
    * @var \Core\Routes\Routes
    */
   protected static $routes;


   /**
    * @var \Core\Routes\Route
    */
   protected static $route;


   /**
    * @var \Core\Request
    */
   protected static $request;

   /**
    * @var \Core\Config
    */
   protected static $config;

   /**
    * @var \Core\Routes\Matcher
    */
   public $matcher;


   /**
    * 
    * Construtor do Router
    * 
    * @param Request $request Objeto da Requisição
    * 
    * @param array|null $routes 
    * 
    * @return void
    * 
    */
   function __construct($routes = NULL) {

      self::$routes = new Routes();

      if (empty(self::$request))
         self::$request = Request::getInstance();

      if (empty(self::$config))
         self::$config = Config::getInstance();

      if (!empty($routes)) {
         if (is_array($routes))
            foreach ($routes as $i => $route)
               self::register($route);
         else
            self::register($routes);
      }

      $this->matcher = New Matcher;

      self::$instance = $this;

   }

   public static function main($options) {
      return self::register(['/', array_merge(['name'=>'home'], $options)]);
   }

   /**
    * 
    * Registra uma rota
    * 
    * @param Route|string|array $route 
    * 
    * @return Route quando sucesso, FALSE quando falha
    * 
    */
   public static function register($route) {
      if (is_array($route)) {
         $r = $route[0];
         $o = !empty($route[1]) ? $route[1] : NULL;
         $c = !empty($route[2]) ? $route[2] : NULL;
         return self::route($r,$o,$c);
      } else if ($route instanceof Route) {
         return self::add($route);
      } else if (is_string($route)) {
         return self::route($route);
      }
      return FALSE;
   }

   /**
    * 
    * Adiciona uma Rota na lista de rotas
    * 
    * @param Route $route Rota a ser adicionada
    * 
    * @return Route
    * 
    */
   public static function add(Route $route) {

      if (!(self::$routes instanceof Routes))
         self::$routes = new Routes();

      if (is_null($route))
         return FALSE;
      
      self::$routes->offsetSet($route->name, $route);

      self::$route = $route;

      return self::$route;
   }

   /**
    * 
    * Adiciona uma rota
    * 
    * @param string $route rota
    * 
    * @param array|null $options | function|null $callback
    * 
    * @param function|null $callback 
    * 
    * @return Route
    * 
    */
   public static function route($route, $name = NULL, $options = NULL, $callback = NULL) {


      if (is_callable($name)) {
         $callback = $name;
         $name = NULL;
      }

      if (is_callable($options)) {
         $callback = $options;
         $options = NULL;
      }

      if (is_array($name)) {
         $options = $name;
         $name = NULL;
      }

      $options = (array) $options;

      if (!is_null($callback)) $options['handler'] = $callback;

      return self::add(New Route($route, $options));
   }

   public function GetByRequest() {
      $matcher = $this->matcher;
      $route = $matcher(self::$request, self::$routes);

      return $route;
   }

   /**
    * 
    * Retorna uma URL relativa
    * 
    * @param array|string|null $params 
    * 
    * @return string
    * 
    */
   public static function href($params = '') {
      $uri = is_string($params) ? $params : self::_href($params);

      if (empty($uri))
         return rtrim(self::$config->url, '/');

      $url = rtrim(self::$config->url, '/') . '/' . ltrim($params, '/');

      return $url;
   }

   /**
    * Retorna uma URL relativa
    * @param array|object|string $route 
    * @return string
    */
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

      return ($uri === 'main/index') ? '' : $uri;
   }

   /**
    * 
    * Retorna a instância do router
    * 
    * @return Request
    * 
    */
   public static function getInstance() {

      if ( !(self::$instance instanceof Router) )
         self::$instance = New self();

      return self::$instance;
   }

} 