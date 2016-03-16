<?php

/**
 * Return the object representation of the array received
 * 
 * @param array to transform in object
 */
function objectify($array){
   if (is_array($array)){
      foreach ($array as $key => $value) {
         if (is_array($value))
            $array[$key] = objectify($value);
      }
   } else
      return $array;

   return (object) $array;
}

/**
 * Removes an item from the array and returns its value.
 *
 * @param array $arr The input array
 * @param $key The key pointing to the desired value
 * @return The value mapped to $key or null if none
 */
function array_remove(array &$arr, $key) {
   if (array_key_exists($key, $arr)) {
      $val = $arr[$key];
      unset($arr[$key]);
      return $val;
   }

   return NULL;
}

function getProtocol(){
   return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
}

function str_a_replace($array, $string) {
   foreach ($array as $from => $to)
      $string = str_replace($from, $to, $string);
   
   return $string;
} 


function genHash($size = 44, $options = array('lower', 'upper', 'nums')) {

   $chars = '';

   if (in_array('lower', $options))
      $chars .= 'abcdefghijklmnopqrstuvwxyz';

   if (in_array('upper', $options))
      $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   if (in_array('nums', $options))
      $chars .= '01234567890123456789';
   if (in_array('special', $options))
      $chars .= '#$%&#$%&#$%&';

   $chars = $chars.$chars.$chars.$chars;

   $output = '';

   for ($i=0; $i < $size; $i++) { 
      $output .= $chars[rand(0,strlen($chars)-1)];
   }

   return $output;

}

/**
 * @func  is_class_method
 * @param $type      "public" / "static" / "private"
 * @param $method    Method name
 * @param $class     Class name
 */

function is_class_method($type="public", $method, $class) {
   // $type = mb_strtolower($type);
   try {
       $refl = new ReflectionMethod($class, $method);

       switch($type) {
           case "static":
           return $refl->isStatic();
           break;
           case "public":
           return $refl->isPublic();
           break;
           case "private":
           return $refl->isPrivate();
           break;
       }
   } catch (Exception $e) {
      exit('Erro: '.$e->getMessage());
   }
} 