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
   if (isset($arr[$key])) {
      $val = $arr[$key];
      unset($arr[$key]);
      return $val;
   }

   return NULL;
}

/**
 * Returns Protocol 'https://' or 'http://' from current server
 * @return string
 */
function getProtocol(){
   return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
}

/**
 * Replace a list array (find=>replace) into a string
 * @param $array
 * @param $string
 * @return mixed
 */
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

/**
 * Return a max size for upload
 *
 * @return void
 */
function file_upload_max_size() {
   static $max_size = -1;
 
   if ($max_size < 0) {
     // Start with post_max_size.
     $post_max_size = parse_size(ini_get('post_max_size'));
     if ($post_max_size > 0) {
       $max_size = $post_max_size;
     }
 
     // If upload_max_size is less, then reduce. Except if upload_max_size is
     // zero, which indicates no limit.
     $upload_max = parse_size(ini_get('upload_max_filesize'));
     if ($upload_max > 0 && $upload_max < $max_size) {
       $max_size = $upload_max;
     }
   }
   return $max_size;
 }
 
 /**
  * Parser for max upload 
  *
  * @param string $size
  * @return void
  */
 function parse_size($size) {
   $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
   $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
   if ($unit) {
     // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
     return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
   }
   else {
     return round($size);
   }
 }