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
   public static $email;

   function __construct() {
      
      $this->apps = (require APPS . 'appsconfig.php');
      $app = APP;
      $this->app = $this->apps->$app;

      $this->device = New MobileDetect();

      if (!defined('MOBILE'))
         define('MOBILE', $this->device->isMobile());
      if (!defined('TABLET'))
         define('TABLET', $this->device->isTablet());

      self::setLanguage();

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