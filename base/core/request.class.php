<?php

namespace Core;

/**
* Request
*/
class Request {
   
   public $post;
   public $get;
   public $request;
   public $session;
   public $headers;
   public $ajax;
   public $method;

   public $target = NULL;
   public $controller  = 'main';
   public $action = 'index';
   public $app;
   public $lost;
   public $language;

   public $params;

   function __construct() {

      if (session_status() == PHP_SESSION_NONE)
         session_start();

      $this->method = $_SERVER['REQUEST_METHOD'];

      $this->parseRoute();

      $this->setVar('post');
      $this->setVar('get');
      $this->setVar('request');
      $this->setVar('session', FALSE);

      $this->ajax = $this->isAjax();

      $this->headers = getallheaders();


   }

   private function parseRoute(){
      $this->params = new \stdClass();

      if (!empty($_GET['uri'])){
         $uri = explode('/', $_GET['uri']);

         //if (empty($this->session['app']))

         if (count($uri) > 0)
            $this->controller = array_shift($uri);

         if (empty($this->controller))
            $this->controller = 'main';

         if (count($uri) > 0)
            $this->action = array_shift($uri);

         if (empty($this->action))
            $this->action = 'index';

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
   }

   public function redirect($params = NULL){

      if (empty($params))
         return $this->header('Location: ' . Route::href());


      if (is_string($params)) {
         return $this->header('Location: ' . $params);
      }

      if (is_array($params)){

         $controller = empty($params['controller']) ? 'main' : $params['controller'];
         $action = empty($params['action']) ? 'index' : $params['action'];

         return $this->header('Location: ' . Route::href("{$controller}/{$action}"));
      } else 
         return $this->header('Location: ' . Route::href());


      exit;
   }

   public function header($content) {
      header($content);
   }

   private function setVar($name, $clear = TRUE) {
      $var = '_'.strtoupper($name);

      global $$var;

      $_VAR = $$var;


      if (!empty($_VAR))
         $this->$name = $_VAR;

      if ($clear)
         $$var = [];
   }

   private function isAjax() {
      return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
   }

   public function session($key, $value = NULL){
      if (is_null($value))
         return empty($_SESSION[$key]) ? NULL : $_SESSION[$key];
      else 
         return $this->session[$key] = $_SESSION[$key] = $value;
      
   }

   public function unsetSession($key) {
      if (isset($_SESSION[$key]))
         unset($_SESSION[$key]);

      if (isset($this->session[$key]))
         unset($this->session[$key]);
   }
} 