<?php
namespace Controller;

use \Core\Controller;
use \Core\SendMail;
/**
* Main Controller
*/
class MainController extends Controller {

   public function index(){

      return $this->load->view('pages/index');

   }
} 