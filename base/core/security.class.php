<?php
namespace Core;

/**
* Security
*/
class Security {

   private $SALT = 'v8$834c9C-$N0l9C@N1AG9#&MvleYQ#pf+%AqkR?sx3d';

   function __construct() {
      $this->CheckSalt();
   }

   public function GenHash($size = 44, $p = array('lower','upper', 'number', 'special')) {
      $l = 'abcdefghijklmnopqrstuvwxyz';
      $u = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $n = '0123456789';
      $s = '!@#$%&*+-?';

      $str = $l . (in_array('upper', $p) ? $u : '' )
                . (in_array('number', $p) ? $n : '' )
                . (in_array('special', $p) ? $s : '' );

      $len = strlen($str);

      $hash = '';
      while (strlen($hash) < $size)
         $hash .= $str[rand(0, $len-1)];

      return $hash;

   }

   public function GetSalt(){
      return $this->SALT;
   }

   private function CheckSalt(){

      if (!is_null($this->SALT))
         return TRUE;

      $file = file(__FILE__);

      $this->SALT = $this->GenHash();

      foreach ($file as &$linha) {
         if ( strpos($linha, 'private $SALT') > 0 && strpos($linha, 'strpos') === FALSE ){
            $linha = str_replace('NULL', "'{$this->SALT}'", $linha);
            break;
         }
      }

      return !!file_put_contents(__FILE__, $file);

   }
} 