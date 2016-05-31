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
      $this->output = $this->load->view('errors/filenotfound', Array(
         'head' => $this->load->view('commons/head'),
         'menu' => $this->load->view('commons/menu'),
         'footer' => $this->load->view('commons/footer'),
         'file' => $this->file
      ));
   }
} 