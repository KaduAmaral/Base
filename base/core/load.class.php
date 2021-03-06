<?php
namespace Core;

use \Core\Routes\Router;
/**
* Load
*/

class Load {

   public $viewbag;
   public $viewdata;
   private $model;
   private $backups;
   private $layout;

   private $hrefs;

   function __construct() {
      $this->viewbag = new \stdClass();
      $this->viewdata = array();
      $this->model = NULL;

      $this->backups = [
         'viewbag' => array(),
         'viewdata' => array(),
         'model' => array(),
         'layout' => array()
      ];
   }

   private function backupData() {
      //$this->backups['viewbag'][] = $this->viewbag;
      //$this->backups['viewdata'][] = $this->viewdata;
      //$this->backups['model'][] = $this->model;
      $this->backups['layout'][] = $this->layout;

      //$this->viewbag = new \stdClass();
      //$this->viewdata = array();
      //$this->model = NULL;
      $this->layout = NULL;
   }

   private function backData() {
      //$this->viewbag = array_pop( $this->backups['viewbag'] );
      //$this->viewdata = array_pop( $this->backups['viewdata'] );
      //$this->model = array_pop( $this->backups['model'] );
      $this->layout  = array_pop( $this->backups['layout'] );
   }

   public function addSrc($placement, $href) {
      $this->hrefs[$placement][] = $href;
   }
   
   public function file($file, $vars = []) {
      if (file_exists($file)){
         return $this->load($file, TRUE, $vars);
      } else throw new \InvalidArgumentException('O arquivo não existe: '.$file);
   }

   public function email($file, $vars = []){
      if (strpos(substr($file, -1*(strlen(VIEWEXTENSION)+1)),'.') === FALSE)
         $file .= VIEWEXTENSION;

      return $this->file($this->getEmailsFolder().$file, $vars);
   }

   public function view($file, $vars = []){
      $this->backupData();

      if (strpos(substr($file, -1*(strlen(VIEWEXTENSION)+1)),'.') === FALSE)
         $file .= VIEWEXTENSION;

      $view = $this->file($this->getViewFolder() . $file, $vars);
      //$view = $file . PHP_EOL;

      if (!empty($this->layout)) {

         $file = 'layouts' . DS . $this->layout;

         //$view .= $this->layout . PHP_EOL;

         $this->layout = null;

         $tmp = $view;

         $vars = $this->hrefs;

         $vars['content'] = $tmp;

         $view = $this->file($this->getViewFolder() . $file . VIEWEXTENSION, $vars);

      }

      $this->backData();

      return $view;
   }

   public function content($content, $vars = []){
      return $this->load($content, FALSE, $vars);
   }

   public static function href($uri = ''){
      return Router::href($uri);
   }

   private function getViewFolder(){
      return Config::getInstance()->views;
   }

   private function getEmailsFolder(){
      if (!empty(Config::getInstance()->email) && !empty(Config::getInstance()->email->templates))
         return Config::getInstance()->email->templates;
      else
         return $this->getViewFolder() . 'emails/';
   }

   public function model($route){

      $route = explode('/'. $route);
      $class = $route[0];
      $action = !empty($route[1]) ? $route[1] : 'index';
      $file = strtolower( MODEL.$class.'Model'.CLASSEXTENSION );

      if (file_exists($file)) {
         require_once $file;
         return New $class();
      } else
         throw New \InvalidArgumentException('A classe não existe: '. $class);
   }

   private function load($___content, $___file, $___vars) {
      ob_start();

      if ($___file){
         if (!empty($___vars) && is_array($___vars)) {
            foreach ($___vars as $___var => $___value)
               $$___var = $___value;
         }

         require_once $___content;
      } else
         echo $this->writeVars($___content, $___vars);

      return ob_get_clean();
   }

   private function writeVars($content, $vars = []){
      if (!empty($vars))
         foreach ($vars as $var => $value)
            $content = str_replace("%{$var}%", $value, $content);

      return $content;
   }
} 