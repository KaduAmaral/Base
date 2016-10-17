<?php

namespace Core;

use \Core\Routes\Router;

/**
* Request
*/
class Request {
   
   static private $instance;

   /**
    * @var array<string, string>
    * 
    * POST da requisição.
    * 
    */
   public $post;

   /**
    * @var array<string, string>
    * 
    * GET da requisição
    * 
    */
   public $get;

   /**
    * @var array<string, string>
    * 
    * REQUEST da requisiçã (POST e GET)
    * 
    */
   public $request;

   /**
    * @var array<string|int, mixin>
    * 
    * Sessão
    * 
    */
   public $session;

   /**
    * @var array<string, string>
    * 
    * Cabeçalhos da requisição
    */
   public $headers;

   /**
    * @var bool
    * 
    * Requisição AJAX
    * 
    */
   public $ajax;

   /**
    * @var string
    * 
    * Método da requisição: GET, POST, etc...
    * 
    */
   public $method;


   /**
    * @var undefined
    * 
    * Não lembro para quê criei esta propriedade.
    * 
    */
   public $target = NULL;

   /**
    * @var string
    * 
    * Controller responsável pela requisição. Este valor será modificado pelo Router::findRoute(Request)
    * 
    */
   public $controller;

   /**
    * @var string
    * 
    * Action responsável pela requisição. Este valor será modificado pelo Router::findRoute(Request)
    * 
    */
   public $action;

   /**
    * @var Controller
    * 
    * Aplicação em execução.
    * 
    */
   public $app;

   /**
    * @var string
    * 
    * Parâmetro sem nome da URL. Parâmetro perdido. 
    * Geralmente é um ID.
    * 
    */
   public $lost;

   /**
    * @var string
    * 
    * Idioma de excução: pt-br, pt-pt, en-us, etc...
    * 
    */
   public $language;

   /**
    * @var array<string, string>
    * 
    * Parâmetros da URL, i.e. /controller/action/pagina/contato -> ['pagina'=>'contato']
    * OU
    * $router->add('/p/:pagina') <- /p/contato -> ['pagina'=>'contato']
    */
   public $params;


   /**
    * @var string
    * 
    * URL da requisição
    */
   public $url;

   /**
    * @var string
    * 
    * URI requisitada.
    * 
    */
   public $uri;

   /**
    * Construtor
    * @return void
    */
   function __construct() {
      if (session_status() == PHP_SESSION_NONE)
         session_start();

      $this->method = $_SERVER['REQUEST_METHOD'];

      $this->setVar('post');
      $this->setVar('get');
      $this->setVar('request');
      $this->setVar('session', FALSE);

      $this->ajax = $this->isAjax();

      $this->headers = getallheaders();

      if (!empty($_SERVER['SCRIPT_URI']))
        $this->url = $_SERVER['SCRIPT_URI'];
      else
        $this->url = $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

      $this->uri = '/' . trim(!empty($_GET['URI']) ? $_GET['URI'] : str_replace(Config::getInstance()->url, '', $this->url), '/');

      self::$instance = $this;
   }


   /**
    * Retorna o controller
    * @return string
    */
   public function getController() {
      return $this->controller;
   }

   /**
    * Retorna a action
    * @return string
    */
   public function getAction() {
      return $this->action;
   }

   /**
    * Realiza o parser da requisição
    * @return void
    * 
    * OBS: Deprecated 
    */
   public function parseRoute(){
      $this->params = new \stdClass();

      $uri = $this->uri;

      if (strpos($uri, '?') > -1) {
        $uri = substr($uri, 0, strpos($uri, '?'));
      }

      $uri = explode('/', trim($uri, '/'));

      $this->controller = count($uri) > 0 ? array_shift($uri) : 'Main';

      $this->action = count($uri) > 0 ? array_shift($uri) : 'index';

      if (count($uri)  > 0){
         $key = NULL;
         foreach ($uri as $value) {
            if (is_null($key))
               $key = $value;
            else {
               $this->params->$key = urldecode($value);
               $key = NULL;
            }
         }

         $this->lost = $key;
      }

   }


   /**
    * 
    * Redireciona a requisição e encerra a execução do script
    * 
    * @param Route|string|null $params 
    * 
    * @return void
    * 
    */
   public function redirect($params = NULL){

      if (empty($params))
         return $this->header('Location: ' . Router::href());


      if (is_string($params)) {
         return $this->header('Location: ' . $params);
      }

      if (is_array($params)){

         $controller = empty($params['controller']) ? 'main' : $params['controller'];
         $action = empty($params['action']) ? 'index' : $params['action'];

         return $this->header('Location: ' . Router::href("{$controller}/{$action}"));
      } else 
         return $this->header('Location: ' . Router::href());


      exit;
   }


   /**
    * 
    * Adiciona um header de saída
    * 
    * @param string $content 
    * 
    * @return void
    * 
    */
   public function header($content) {
      header($content);
   }


   /**
    * 
    * Seta uma variável interna a partir da sua GLOBAL equivalente
    * 
    * @param string $name 
    * 
    * @param bool $clear limpa a sua equivalente GLOBAL
    * 
    * @return void
    * 
    */
   private function setVar($name, $clear = TRUE) {
      $var = '_'.strtoupper($name);

      global $$var;

      $_VAR = $$var;

      if (!empty($_VAR))
         $this->$name = $_VAR;

      if ($clear)
         $$var = [];
   }


   /**
    * 
    * Retorna se a requisição é ajax.
    * 
    * @return bool
    * 
    */
   private function isAjax() {
      return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
   }


   /**
    * 
    * Seta ou retorna o valor de uma variável da sessão
    * 
    * @param string $key 
    * 
    * @param mixin|null $value Se null retorna o valor da variável, senão seta-o
    * 
    * @return mixin
    * 
    */
   public function session($key, $value = NULL){
      if (is_null($value))
         return !isset($_SESSION[$key]) ? NULL : $_SESSION[$key];
      else 
         return $this->session[$key] = $_SESSION[$key] = $value;
      
   }

   /**
    * 
    * Exclui uma variável da sessão.
    * 
    * @param string $key 
    * 
    * @return void
    * 
    */
   public function unsetSession($key) {
      if (isset($_SESSION[$key]))
         unset($_SESSION[$key]);

      if (isset($this->session[$key]))
         unset($this->session[$key]);
   }


   /**
    * 
    * Retorna o método da requisição
    * 
    * @return string
    * 
    */
   public function getMethod() {
      return $this->method;
   }

   /**
    * 
    * Retorna a instância da requisição
    * 
    * @return Request
    * 
    */
   public static function getInstance() {

      if ( !(self::$instance instanceof self) )
         self::$instance = New self();

      return self::$instance;
   }

} 