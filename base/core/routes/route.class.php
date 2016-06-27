<?php
namespace Core\Routes;
/**
* 
*/
class Route {

   private $defaults = [];

   /**
    * Rota
    * 
    * @var string
    */
   protected $route;

   /**
    * @var string
    */
   protected $name;

   /**
    * Padrão da Rota
    * 
    * @var string: regex
    */
   protected $pattern;

   /**
    * Idioma
    * 
    * @var string: 'pt-br'
    * 
    */
   protected $lang;

   /**
    * Host
    * 
    * @var string: url | domain
    */
   protected $host;

   /**
    * Controller da Rota
    * 
    * @var string
    * 
    */
   protected $controller;

   /**
    * Action da Rota
    * 
    * @var string
    * 
    */
   protected $action;


   /**
    * Regras da Rota
    * 
    * @var array<RouteRule>
    * 
    */
   protected $rules = [];

   /**
    * Handler
    * 
    * @var function
    * 
    * @default function( [ mixin $params [, ...]] ) use (Controller $controller, Request $request){
    *             return $controller->{$request->getAction()}();
    *          }
    */
   private $handler;

   /**
    * Métodos Permitidos
    * @var array<string>
    * 
    */
   private $allows = [];

   /**
    * @var array<string:param, string:regex>
    */
   private $params = [];

   /**
    * @var array<string:param, string:param-value>
    */
   private $attributes = [];

   /**
    * 
    * Construtor
    * 
    * @param string $route Rota, i.e.: /artigo/:name
    * 
    * @param array|null $options Configuraçõe da Rota
    * 
    * @return void
    * 
    */
   public function __construct($host, $options = NULL) {
      if (is_null($options)) $options = [];

      if (is_array($host))
         $options = array_merge($options, $host);
      else if (is_string($host))
         $options['host'] = $host;

      if (empty($options['rules']))
         $options['rules'] = [
            new \Core\Routes\Rules\Allows,
            new \Core\Routes\Rules\Host
         ];

      if (!isset($options['params'])) $options['params'] = [];

      $options['params'] = array_merge($this->defaults, $options['params']);

      if (!empty($options))
         $this->settings($options);

   }

   /**
    * 
    * Seta as configurações da Rota
    * 
    * @param array<string, mixin> $options 
    * 
    * @return Route
    * 
    */
   public function settings($options) {
      if (empty($options))
         return $this;

      if (!empty($options['host']) && empty($options['pattern']))
         $options['pattern'] = $options['host'];

      if (empty($options['name']))
         $options['name'] = str_replace('/', '.', trim($options['host'], '/'));

      foreach ($options as $p => $value)
         if (is_callable([$this, $p]))
            $this->$p($value);

      return $this;
   }


   /**
     *
     * Quando a rota for clonada, reseta os parâmetros `$params` para os padrões.
     *
     */
   public function __clone() {
      // $this é a instância clonada, não a original
      // $this->name = NULL;
      // $this->host = NULL;
      // $this->pattern = NULL;
      $this->attributes = [];
   }

   public function _clone($options = NULL) {

      $route = clone $this;
      $route->name = NULL;
      $route->host = NULL;
      $route->pattern = NULL;

      $route->settings($options);

      return $route;
   }


   /**
    * 
    * Seta os métodos permitidos
    * 
    * @param string|array $allows 
    * 
    * @return Route
    * 
    */
   public function allows($allows) {
      $this->allows = array_merge($this->allows, (array) $allows);
      return $this;
   }


   /**
    * 
    * Seta os métodos permitidos
    * 
    * @param callable|function|object $allows 
    * 
    * @return Route
    * 
    */
   public function rules($rule) {
      $this->rules = array_merge($this->rules, (array) $rule);
      return $this;
   }


   /**
    * 
    * Seta os parâmetros
    * 
    * @param type $params 
    * 
    * @return type
    * 
    */
   public function params($params) {
      $this->params = array_merge($this->params, (array) $params);

      return $this;
   }

   /**
    * 
    * Seta os parâmetros
    * 
    * @param type $attributes 
    * 
    * @return type
    * 
    */
   public function attributes($attributes) {
      $this->attributes = array_merge($this->attributes, (array) $attributes);
      return $this;
   }

   /* Propriedades Imutáveis */

   /**
    * 
    * Seta a rota
    * 
    * @param  string $route 
    * 
    * @return Route
    * 
    */
   public function route($route) {
      $this->setImutableProperty('route', $route);
      return $this;
   }

   /**
    * 
    * Seta a rota
    * 
    * @param  string $name 
    * 
    * @return Route
    * 
    */
   public function name($name) {
      $this->setImutableProperty('name', $name);
      return $this;
   }


   /**
    * 
    * Seta a rota
    * 
    * @param  string $route 
    * 
    * @return Route
    * 
    */
   public function pattern($value) {
      $pattern = '/^' . str_replace('/', '\/', $value) . '$/';
      $this->setImutableProperty('pattern', $pattern);
      return $this;
   }


   /**
    * 
    * Seta o controller
    * 
    * @param string $controller
    * 
    * @return Route
    * 
    */
   public function controller($controller) {
      $this->setImutableProperty('controller', $controller);
      return $this;
   }


   /**
    * 
    * Seta o action
    * 
    * @param string $action
    * 
    * @return Route
    * 
    */
   public function action($action) {
      $this->setImutableProperty('action', $action);
      return $this;
   }


   /**
    * 
    * Seta o idioma
    * 
    * @param string $lang: 'pt-br' | 'pt-pt' | 'en-us'
    * 
    * @return Route
    * 
    */
   public function lang($lang) {
      $this->setImutableProperty('lang', $lang);
      return $this;
   }


   /**
    * 
    * Seta o controller
    * 
    * @param string $controller
    * 
    * @return Route
    * 
    */
   public function host($host) {
      $this->setImutableProperty('host', $host);
      return $this;
   }

   public function setHost() {
      $host = $this->route;
      
      if (!empty($this->params))
         foreach ($this->params as $param => $ex)
            $host = str_replace(":{$param}", $ex, $host);
      

      return $this->host($host);

   }

   /**
    * 
    * Seta o Handler, função de execução
    * 
    * @param function $handler 
    * 
    * @return Route
    * 
    */
   public function handler($handler) {
      if (is_callable($handler))
         $this->setImutableProperty('handler', $handler);
      else
         throw new \InvalidArgumentException ("O Handler deve ser uma função/método executável.");

      return $this;
   }

   /**
    * 
    * Seta uma variável imutável
    * 
    * @param string $prop 
    * 
    * @param mixin $value 
    * 
    * @return Route
    * 
    */
   private function setImutableProperty($prop, $value) {
     if ($this->$prop !== null) {
         $message = __CLASS__ . '::$'.$prop.' é imutavel uma vez setada.';
         throw new \Core\Exception\ImmutablePropertyException($message);
     }

     $this->$prop = $value;

     return $this;
   }

   /**
    * 
    * Retorna o controller da rota
    * 
    * @return Controller
    * 
    */
   public function getController() {
      $controller = "\Controller\{$this->controller}Controller";
      return New $controller();
   }

   /**
    * 
    * Retorna o valor de uma propriedade
    * 
    * @param string $key 
    * 
    * @return mixin
    * 
    */
   public function __get($key) {
      return $this->{$key};
   }

   public function __isset($key) {
      if (isset($this->{$key})) {
         return (FALSE === empty($this->{$key}));
      } else {
         return NULL;
      }
   }

} 