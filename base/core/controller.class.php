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
   protected $output = '';

   public $route;
   public $request;
   protected $load;
   protected $model;
   protected $config;
   protected $connection;

   function __construct($request) {

      //$this->request = New Request();
      $this->request = $request;
      $this->route   = New Route();
      $this->config  = New Config();
      $this->load    = New Load();

      if (!empty($this->config->app->database)){
         $this->connection = New Connection($this->config->app->database);

         Model::$connection = $this->connection;
      }

      Load::$config = $this->config;

      $this->checkPermission();

   }

   public function execute($param = NULL){

      $action = $this->request->action;

      if (!is_null($param))
         $this->$action($param);
      else 
         $this->$action();

   }

   private function checkPermission() {
      //return true;
      if ($this->config->app->authentication) {

         $authentication = $this->config->app->authentication;

         $auth = "\\Controller\\{$authentication->controller}Controller";
         $check = $authentication->action;

         if (!($auth::$check())){
            if (!property_exists($authentication->routes, $this->request->controller)){
               $this->request->redirect(Route::href("{$authentication->redirect->controller}/{$authentication->redirect->action}"));
            }
            else if (
               property_exists($authentication->routes, $this->request->controller) && 
               !in_array($this->request->action, (array)$authentication->routes->{$this->request->controller})
            ){
               $this->request->redirect(Route::href("{$authentication->redirect->controller}/{$authentication->redirect->action}"));
            }
         }
      }

      return TRUE;
   }

   public function output($print = TRUE){
      $output = $this->output;
      $this->output = '';

      Model::$connection = NULL;
      $this->connection = NULL;
      
      if ($print) echo     $output;
      else        return   $output;
   }

} 