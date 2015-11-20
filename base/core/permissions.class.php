<?php
namespace Core;

use \Core\Route;
/**
* Roda o inicio da Application
* Com base na URL o Objeto Request avalia qual é a classe e o método a ser executado
*/

class Permissions {

   public static function check() {

      if (empty($_SESSION['permissions']))
         self::load();



         foreach ($_SESSION['permissions'] as $i => $value) {
            
            if(@preg_match($value['route'], $url) || $value['route'] == $url){
               return $value['allow'];
            }
         }

      
      return false;
   }

   public static function load(){
      $Route = Route::guest();
      foreach ($Route as $route => $array) {
         if(@preg_match($route, $url) || $route == $url){
            return $Route[$route];
         }
      }
   }

} 