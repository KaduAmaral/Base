<?php
namespace Core;

use Core\Routes\Route;
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
    * @var array
    */
   protected $services = [];

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
   function __construct(Request $request, Route $route) {

      $this->request =  $request;
      $this->config  = Config::getInstance();

      $this->router   = New Router();
      $this->route = $route;
      $this->load    = New Load();

      $this->startConnection();

      $this->checkPermission();

      Model::$controller = $this;
   }

   /**
    * @param string $name
    * @return Service
    */
   protected function service($name, $args = []) {
      if (isset($this->services[ strtolower($name) ])) return $this->services[ strtolower($name) ];

      try {
         $service = $this->loadClass($name, "\\Services\\", 'Service', $args);
      } catch (\Exception $e) {
         throw new \InvalidArgumentException('O serviço informado não existe.');
      }
      return $this->services[ strtolower($name) ] = $service;
   }

   /**
    * @param string $name
    * @return mixed
    */
   protected function handle($name) {
      $data = !empty($this->request->post[$name]) ? $this->request->post[$name] : [];
      return $this->loadClass($name, "\\Model\\", "Model", $data);
   }

   /**
    * @param string $name
    * @param string $namespace
    * @param string $sufix
    * @param array  $args
    * @return mixed
    */
   private function loadClass($name, $namespace = "\\", $sufix = "", $args = []) {
      $class = $namespace . $name . $sufix;

      if (!class_exists($class))
         throw new \InvalidArgumentException('A classe informada não existe');

      $class = new \ReflectionClass($class);

      return $class->newInstanceArgs($args);
      //return new $class(...$args);
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

         $route = New Route("/{$controller}/{$action}", [
            'controller' => $class,
            'action'    => $action,
            'params' => $args
         ]);

         $app = New $class($request, $route);

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
            !empty($this->config->authentication->notcheckon->{$this->route->getController()}) &&
            (
               $this->config->authentication->notcheckon->{$this->route->getController()} === '*' ||
               in_array($this->route->action, (array)$this->config->authentication->notcheckon->{$this->route->getController()})
            )
         ) {
            return TRUE;
         }

         if (FALSE && $this->route->getController() == 'error') {

            echo "\n\n";
            var_dump($this->route->getController());
            echo "\n\n";
            var_dump($this->config->authentication->notcheckon);
            echo "\n\n";
            var_dump($this->config->authentication->notcheckon->{$this->route->getController()} === '*');
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