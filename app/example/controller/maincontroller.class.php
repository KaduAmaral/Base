<?php
namespace Controller;

/**
* Main Controller
*/
class MainController extends \Core\Controller {

   public function index(){
      return $this->load->view('pages/index');
   }
} 