<?php
namespace Core;

use Core\Routes\Router;
/**
* Controller
*/
class Controller {

   /**
    * Identifica se é pra imprimir o retorno ou não.
    * @var bool
    */
   public $outputreturn = TRUE;

   /**
    * Output a ser impresso
    * @var string
    */
   public $output = '';

   /**
    * @var Router
    */
   public $router;

   /**
    * @var Router
    */
   public $route;

   /**
    * @var Request
    */
   public $request;

   /**
    * @var array
    */
   public $cache = [];

   /**
    * @var Load
    */
   protected $load;

   /**
    * @var Model
    */
   protected $model;

   /**
    * @var Config
    */
   protected $config;

   /**
    * @var Connection
    */
   public static $connection = NULL;

   /**
    * Controller constructor.
    * @param Request|NULL $request
    */
   function __construct(Request $request = NULL) {

      $this->request = is_null($request) ? Request::getInstance() : $request;
      $this->config  = Config::getInstance();

      $this->router   = New Router();
      $this->route = $this->router;
      $this->load    = New Load();

      $this->startConnection();

      $this->checkPermission();

      Model::$controller = $this;
   }

   /**
    * Executa o controlador. Em breve será papel do Dispatch
    * @param mixed $param
    * @return string
    * @throws Exception
    */
   public function execute($param = NULL){

      if (!is_callable([$this, $this->request->action]))
         throw new Exception('Requisição inválida');

      if (!is_array($param)) $param = [$param];

      return call_user_func_array([$this, $this->request->action], (array) $param);
   }

   /**
    * Executa um determinado controller
    * @param string $controller Nome do Controller
    * @param string $action Método do Controller
    * @param array $args Argumentos para o Controller
    * @return mixed
    * @throws Exception
    */
   public function controller($controller, $action = 'index', $args = NULL) {

      $class = "\\Controller\\{$controller}Controller";

      if (class_exists($class)) {

         $request = clone $this->request;

         $request->controller = $controller;
         $request->action = $action;
         $request->params = $args;

         $app = New $class($request);

         if (method_exists($app, $action)) {
            if (is_array($args) && count($args) > 0)
               return call_user_func_array([$app, $action], $args);
            else
               return $app->$action();
         } else {
            throw new Exception('Requisição inválida.');
         }

      } else {
         throw new Exception('Requisição inválida.');
      }

   }

   private function checkPermission() {
      //return true;

      if (!empty($this->config->authentication)) {

         $authentication = $this->config->authentication;

         $auth = $authentication->class;
         $check = $authentication->method;

         if (
            !empty($this->config->authentication->notcheckon) && 
            !empty($this->config->authentication->notcheckon->{strtolower($this->request->controller)}) &&
            (
               $this->config->authentication->notcheckon->{strtolower($this->request->controller)} === '*' ||
               in_array($this->request->action, (array)$this->config->authentication->notcheckon->{strtolower($this->request->controller)})
            )
         ) {
            return TRUE;
         }

         if (FALSE && $this->request->controller == 'error') {
            var_dump($this->request->controller);
            echo '\n\n';
            var_dump($this->config->authentication->notcheckon);
            exit;
            
         }


         if (method_exists($auth, 'setController')) {
            $auth::setController($this);
         }

         $auth::$check();

      }

      return TRUE;
   }

   public function setOutput($output) {
      $this->output = $output;
   }

   public function appendOutput($output) {
      $this->output = $this->output . $output;
   }

   public function prependOutput($output) {
      $this->output = $output . $this->output;
   }

   public function output($print = TRUE, $clear = TRUE){
      $output = $this->output;

      if ($clear)
         $this->output = '';

      $this->closeConnection();

      if ($print) echo     $output;
      else        return   $output;
   }

   public function startConnection() {


      if (!empty($this->config->database) && is_null(self::$connection)){
         self::$connection = New Connection($this->config->database);

         if (defined('DEBUG') && DEBUG === TRUE)
            self::$connection->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );

         Model::_setConnection(self::$connection);
      } else {

      }
   }

   public function closeConnection(){
      if (!is_null(self::$connection)){
         Model::$connection = NULL;
         self::$connection = NULL;
      }
   }

} 