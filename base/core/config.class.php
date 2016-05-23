<?php
namespace Core;

require_once ADDONS.'INI'.DS.'INI.class.php';
require_once ADDONS.'MobileDetect'.DS.'MobileDetect.class.php';

use \INI;
use \Addons\MobileDetect\MobileDetect;
/**
* Config
*/
class Config {
   private static $lang;

   public $apps;
   public $app;
   public $device;

   public static $config;
   public static $email = NULL;

   function __construct() {
      
      $this->apps = (require APPS . 'appsconfig.php');
      $app = APP;
      $this->app = $this->apps->$app;

      if (!empty($this->app->email))
         self::$email = $this->app->email;

      $this->setDefaults();

      $this->device = New MobileDetect();

      if (!defined('MOBILE'))
         define('MOBILE', $this->device->isMobile());
      if (!defined('TABLET'))
         define('TABLET', $this->device->isTablet());


      self::setLanguage();
   }

   private function setDefaults(){
      if (empty($this->app->view))
         $this->app->view = APPS . APP . DS . 'view' . DS;

      if (empty($this->app->emails))
         $this->app->emails = $this->app->view . 'emails' . DS;

      if (!defined('URL') && !empty($this->app->url)) 
         define('URL', $this->app->url);

      if (empty($this->app->url) && defined('URL'))
         $this->app->url = URL;
   }

   public static function getLanguage(){
      return self::$lang;
   }

   public static function setLanguage($lang = NULL){

      // Idioma padrão
      if (is_null($lang)) $lang = LANG;


      if (empty($_COOKIE['language']))
         setcookie('language', $lang, time()+60*60*24*365*5, '/'); 

      self::$lang = $lang;
   }


} 