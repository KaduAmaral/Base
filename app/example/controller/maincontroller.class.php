<?php
namespace Controller;

use \Core\Controller;
use \Core\Security;

/**
* Main Controller
*/
class MainController extends Controller
{
   
   public function index(){

      $this->output = $this->load->view('pages/index');

   }
} 