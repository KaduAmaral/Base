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