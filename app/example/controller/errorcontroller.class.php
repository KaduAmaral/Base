<?php
namespace Controller;
use \Core\Controller;
/**
* Error
*/
class ErrorController extends Controller {

   function index($message = NULL) {
      return $this->load->view('errors/filenotfound', [
         'url' => $this->request->url
      ]);
   }
} 