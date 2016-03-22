<?php
namespace Core;
use \Core\Request;
use \Core\Route;
use \Core\Load;
use \Core\Config;
use \Core\Connection;
use \Core\Model;
/**
* Controller
*/
class Controller {
   public $outputreturn = TRUE;
   private $output = '';

   public $route;
   public $request;
   public $cache = [];
   protected $load;
   protected $model;
   protected $config;

   public static $connection = NULL;

   function __construct($request) {

      //$this->request = New Request();
      $this->request = $request;
      $this->config  = New Config();
      $this->route   = New Route();
      $this->load    = New Load();


      $this->startConnection();

      Load::$config = $this->config;

      $this->checkPermission();

      Model::$controller = $this;
   }

   public function execute($param = NULL){


      $action = $this->request->action;

      if (!method_exists($this, $action))
         throw new \Exception("Requisição inválida", 1);
         

      if (!is_null($param)){
         return $this->$action($param);
      }
      else 
         return $this->$action();

   }

   public function controller($controller, $action = 'index'){


      $class = "\\Controller\\{$controller}Controller";
      if (class_exists($class)) {

         $request = clone $this->request;


         $request->controller = $controller;
         $request->action = $action;

         $app = New $class($request);

         if (method_exists($app, $action)) {
            return $app->$action();
         } else {
            throw new \Exception('Requisição inválida.');
         }
      } else {
         throw new \Exception('Requisição inválida.');
      }
   }

   private function checkPermission() {
      //return true;


      if (!empty($this->config->app->authentication)) {


         $authentication = $this->config->app->authentication;

         $auth = $authentication->class;
         $check = $authentication->method;

         if (
            !empty($this->config->app->authentication->notcheckon) && 
            !empty($this->config->app->authentication->notcheckon->{strtolower($this->request->controller)}) &&
            (
               $this->config->app->authentication->notcheckon->{strtolower($this->request->controller)} === '*' ||
               in_array($this->request->action, (array)$this->config->app->authentication->notcheckon->{strtolower($this->request->controller)})
            )
         ) {
            return TRUE;
         }

         if (FALSE && $this->request->controller == 'error') {
            var_dump($this->request->controller);
            echo '\n\n';
            var_dump($this->config->app->authentication->notcheckon);
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
      if (!empty($this->config->app->database) && is_null(self::$connection)){
         self::$connection = New Connection($this->config->app->database);

         Model::setConnection(self::$connection);
      }
   }

   public function closeConnection(){
      if (!is_null(self::$connection)){
         Model::$connection = NULL;
         self::$connection = NULL;
      }
   }

} 