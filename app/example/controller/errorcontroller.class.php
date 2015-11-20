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

   function index() {
      $this->output = $this->load->view('errors/filenotfound.phtml', Array(
         'head' => $this->load->view('commons/head.phtml'),
         'menu' => $this->load->view('commons/menu.phtml'),
         'footer' => $this->load->view('commons/footer.phtml'),
         'file' => $this->error,
         'error' => $this->message
      ));
   }
} 