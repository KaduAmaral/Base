<?php
namespace Controller;
use \Core\Controller;
/**
* FileNotFoundController
*/
class FileNotFoundController extends Controller
{
   public $file;
   
   function index() {
      $this->output = $this->load->view('errors/filenotfound.phtml', Array(
         'head' => $this->load->view('commons/head.phtml'),
         'menu' => $this->load->view('commons/menu.phtml'),
         'footer' => $this->load->view('commons/footer.phtml'),
         'file' => $this->file
      ));
   }
} 