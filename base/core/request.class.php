<?php

namespace Core;

use Core\Routes\Route;
use \Core\Routes\Router;
use \Core\Request\Globals;

/**
* Request
*/
class Request {

   const METHOD_HEAD = 'HEAD';
   const METHOD_GET = 'GET';
   const METHOD_POST = 'POST';
   const METHOD_PUT = 'PUT';
   const METHOD_PATCH = 'PATCH';
   const METHOD_DELETE = 'DELETE';
   const METHOD_PURGE = 'PURGE';
   const METHOD_OPTIONS = 'OPTIONS';
   const METHOD_TRACE = 'TRACE';
   const METHOD_CONNECT = 'CONNECT';

   /**
    * @var Request
    */
   static private $instance;

   /**
    * Variávels globais;
    *
    * @var array
    */
   public $globals;

   /**
    * Dados de POST da Requisição
    * @var array
    */
   public $post;

   /**
    * Dados de GET da Requisição
    * @var array
    */
   public $get;

   /**
    * Dados de REQUEST da Requisição
    * @var array
    */
   public $request;

   /**
    * Dados da Sessão da Requisição
    * @var array
    */
   public $session;

   /**
    * Cabeçalhos da Requisição
    * @var array
    */
   public $headers;

   /**
    * Identifica se requisição é ajax
    *
    * @var bool
    */
   public $ajax;

   /**
    * Método da requisição: GET, POST, etc...
    * @var string
    */
   public $method;


   /**
    * Não lembro para quê criei esta propriedade.
    *
    * @deprecated
    * @var undefined
    */
   public $target = NULL;

   /**
    * Controller responsável pela requisição. Este valor será modificado pelo Router::findRoute(Request)
    * @var string
    */
   public $controller;

   /**
    * Action responsável pela requisição. Este valor será modificado pelo Router::findRoute(Request)
    * @var string
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
    * @var string
    */
   public $queryString;

   /**
    * @var string
    */
   public $body;

   /**
    * Construtor
    * @return void
    */
   function __construct() {
      self::$instance = $this;

      if (php_sapi_name() == "cli") {
         return $this;
      }


      $this->globals = new Globals();

      $this->setVar('post');
      $this->setVar('get');
      $this->setVar('request');
      $this->setVar('session', FALSE);

      $this->ajax = $this->isAjax();

      $this->headers = $this->loadHeaders();

      if (!empty($_SERVER['SCRIPT_URI']))
        $this->url = $_SERVER['SCRIPT_URI'];
      else
        $this->url = strtolower($_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME']);

      $this->uri = $_SERVER['REQUEST_URI'];

      $baseuri = Config::getInstance()->base;
      if (!empty($baseuri) && strpos($this->uri, $baseuri) === 0) {
         $this->uri = substr($this->uri, 0, strlen($baseuri));
      }

      $this->queryString = strpos($this->uri, '?') > -1 ? substr($this->uri, strpos($this->uri, '?')+1) : null;

      $this->uri = strpos($this->uri, '?') > -1 ? substr($this->uri, 0, strpos($this->uri, '?')) : $this->uri;

      $this->loadBody();


   }

   private function loadHeaders() {
      if (function_exists('getallheaders')) {
         return getallheaders();
      } else {
         if (!is_array($_SERVER)) {
            return array();
         }
         $headers = array();
         foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
               $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
               $headers[$key] = $value;
            }
         }
         return $headers;
      }
   }

   public function loadBody() {
      $this->body = file_get_contents('php://input');

      if (
         (!empty($this->headers['Content-Type']) && $this->headers['Content-Type'] == 'application/json') ||
         (!empty($this->headers['X-Content-Type']) && $this->headers['X-Content-Type'] == 'application/json')
      ) {
         $this->body = json_decode($this->body, true);
      }
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
    * @deprecated
    */
   public function parseRoute(){
      $this->params = new \stdClass();

      $uri = $this->uri;

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

   public function getRouteByRequest() {
      $this->parseRoute();
      
      $r = \Core\Routes\Router::get($this->uri, 'request_runtime_route', [
          'controller' => $this->controller,
          'action' => $this->action
      ]);

      if  ($this->params)
         $r->params( (array) $this->params )->attributes( (array) $this->params );

      return $r;
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

      if (empty($params)){
         $this->header('Location: ' . Router::href());
      } else if (is_string($params)) {
         $this->header('Location: ' . $params);
      } else if (is_array($params)){

         $controller = empty($params['controller']) ? 'main' : $params['controller'];
         $action = empty($params['action']) ? 'index' : $params['action'];

         $this->header('Location: ' . Router::href("{$controller}/{$action}"));
      } else if ($params instanceof Route) {
         $this->header('Location: ' . $params->setHost()->host);
      } else
         $this->header('Location: ' . Router::href());

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