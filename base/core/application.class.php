<?php
namespace Core;

use \Exception;
use \Core\Request;
use \Controller\ErrorController;
/**
* Roda o inicio da Application
* Com base na URL o Objeto Request avalia qual é a classe e o método a ser executado
*/

class Application {

   public static function RUN($app = null) {


      if (!defined('APP') && !is_null($app))
         define('APP', $app);

      $request = New Request();
      $class = '\\Controller\\'.$request->controller.'Controller';

      // Retorno caso configuração $outputreturn do controller seja true
      $output = '';

      try {

         if (class_exists($class)) {
            $app = New $class($request);
         } else {
            throw new Exception("Requisição inválida");
         }

      } catch (Exception $e) {
         $app = New ErrorController($request);
         $app->setOutput($app->index());
         $app->output();
         return FALSE;
      }


      if (!empty($request->post['mvc:model'])){
         $model = '\Model\\' . array_remove($request->post, 'mvc:model') . 'Model';
         try {
            $param = New $model($request->post);
         } catch (Exception $e) {
            $app->setOutput($app->index());
            $app->output();
            return FALSE;
         }
      } else if (empty($request->lost) && !is_numeric($request->lost))
         $param = NULL;
      else {
         $param = $request->lost;
      }


      try {
         $output = $app->execute($param);
      } catch (Exception $e) {
         $app = New ErrorController($request);
         $app->setOutput($app->index());
         $app->output();
         return FALSE;
      }

      if ($app->outputreturn)
         $app->setOutput($output);

      $app->output();
   }
} 