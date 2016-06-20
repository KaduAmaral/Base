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
   public static $routes;


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

   public static function main($options = NULL) {
      if (is_null($options))
         return self::$routes['home'];
      else
         return self::register(['/', array_merge(['name'=>'home'], $options)]);
   }

   public static function notfound($options = NULL) {
      return self::error(404, $options);
   }

   public static function error($code, $options = NULL) {
      if (empty($options))
         return self::$routes["error.{$code}"];
      else
         return self::register(["/{$code}", array_merge(['name' => "error.{$code}"], $options)]);
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
         return forward_static_call_array(['self','route'], $route);
         //return call_user_func_array(self::route, $route);
         // return self::route($r,$o,$c);
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

      //self::$routes->offsetSet($route->name, $route);

      self::$routes[$route->name] = $route;

      self::$route = $route;

      return self::$route;
   }

   /**
    * 
    * Adiciona uma rota
    * 
    * @param string $host Endereço da rota, ex: /postagem, /postagem/:id, /artigo/:slug, etc
    * 
    * @param string|null $name Nome da rota (para identificação), deve ser único. (optional)
    * 
    * @param array|null $options Opções da rota [controller, action, etc..]
    * 
    * @param function|null $callback Callback da rota . (não implementado ainda)
    * 
    * @param type|null $route Objeto Route, ex: (New Route([host:home,controller:main,action:index,name:home]))
    * 
    * @execution Router::route('/contato',['controller'=>'contato','action'=>'index']);
    * 
    * @execution Router::route('/contato', 'pagina.contato', ['controller'=>'contato','action'=>'index']);
    * 
    * @execution Router::route('/contatar', 'pagina.contatar', [
    *    'controller'=>'contato',
    *    'action'=>'index'
    * ], Router::GetByName('pagina.contato'));
    * 
    * @execution Router::route('/contatar', Router::GetByName('pagina.contato'))
    * 
    * @return Route
    * 
    */
   public static function route($host, $name = NULL, $options = NULL, $callback = NULL, $route = NULL) {

      $options = self::parseRouteOptions($host, $name, $options, $callback, $route);

      $route = self::parseRouteRoute($host, $name, $options, $callback, $route);

      $callback = self::parseRouteCallback($host, $name, $options, $callback, $route);

      if (is_callable($callback)) $options['handler'] = $callback;

      if (!empty($name) && is_string($name)) $options['name'] = $name;

      if (is_null($route))
         $route = New Route($host, $options);
      else {
         $options['host'] = $host;
         $route = $route->_clone($options);
      }

      return self::add($route);
   }

   private static function parseRouteOptions($host, $name = NULL, $options = NULL, $callback = NULL, $route = NULL) {

      if (is_array($host)) $options = $host;

      if (is_array($name)) $options = $name;

      if (!is_array($options)) $options = [];

      return $options;
   }

   private static function parseRouteCallback($host, $name = NULL, $options = NULL, $callback = NULL, $route = NULL) {

      if (is_callable($host)) $callback = $host;

      if (is_callable($name)) $callback = $name;

      if (is_callable($options)) $callback = $options;

      if (is_callable($route))  $callback = $route;

      if (!is_callable($callback)) $callback = NULL;

      return $callback;
   }

   private static function parseRouteRoute($host, $name = NULL, $options = NULL, $callback = NULL, $route = NULL) {

      if ($host instanceof Route) $route = $host;

      if ($name instanceof Route) $route = $name;

      if ($options instanceof Route) $route = $options;

      if ($callback instanceof Route) $route = $callback;

      if (!($route instanceof Route))  $route = NULL;

      return $route;
   }

   public static function GetByName($name) {
      if (!isset(self::$routes[$name]))
         return NULL;

      return self::$routes[$name];
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
         New self();

      return self::$instance;
   }

} 