<?php
namespace Controller;
use \Core\Controller;
/**
* Error
*/
class ErrorController extends Controller {
   public $name;
   public $message;
   public $error;

   function index($message = NULL) {
      if (!empty($message)) $this->message = $message;
      return $this->load->view('errors/filenotfound', Array(
         'head' => $this->load->view('commons/head'),
         'menu' => $this->load->view('commons/menu'),
         'footer' => $this->load->view('commons/footer'),
         'file' => $this->error,
         'error' => $this->message
      ));
   }
} 