<?php
namespace Controller;

use \Core\Controller;

/**
* Main Controller
*/
class MainController extends Controller
{
   
   public function index(){

      return $this->load->view('pages/index');

   }
} 