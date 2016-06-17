<?php
namespace Controller;

use \Core\Controller;
/**
* 
*/
class LoginController extends Controller
{
   
   function index() {
      return $this->load->content(
         '%header% %menu%'.
         '<div class="container"><div class="col-xs-12">'.
         'Página de Login (ou era pra ser :P). <a href="'. $this->route->href() .'">Voltar</a>'.
         '</div></div>'.
         '%footer%',
         Array(
            'header' => $this->load->view('commons/head'),
            'menu'   => $this->load->view('commons/menu'),
            'footer' =>$this->load->view('commons/footer')
         )
      );
   }
} 