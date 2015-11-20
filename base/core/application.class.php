<?php
namespace Core;

use \Core\Request;
use \Core\Exception\SystemException;
use \Core\Exception\Exceptions;

/**
* Roda o inicio da Application
* Com base na URL o Objeto Request avalia qual é a classe e o método a ser executado
*/

class Application {

   public static function RUN() {

      $request = New Request();

      $class = '\\Controller\\'.$request->controller.'Controller';

      if (!empty($request->post['mvc:model'])){
         $model = '\Model\\' . array_remove($request->post, 'mvc:model') . 'Model';
         try {
            $param = New $model($request->post);
         } catch (Exception $e) {
            $app = New \Controller\ErrorController($request);
            $app->message = $e->getMessage();
            $app->error = $model;
            $app->index();
         }
      } else if (!empty($request->lost))
         $param = $request->lost;
      else {
         $param = NULL;
      }

      try {
         
         $app = New $class($request);
         $app->execute($param);
         
         
      } catch (SystemException $e) {
         if ( strpos(Exceptions::E_FILENOTFOUND.'|'.Exceptions::E_CLASSNOTEXIST, $e->getCode()) !== FALSE){
            $app = New \Controller\FileNotFound($request);
            $app->file = $class;
            $app->index();
         } else {
            $app = New \Controller\ErrorController($request);
            $app->message = $e->getMessage();
            $app->error = $class;
            $app->index();
         }

      } catch (Exception $e) {
         $app = New \Controller\ErrorController($request);
         $app->message = $e->getMessage();
         $app->error = $class;
         $app->index();
      }


      $app->output();
   }
} 