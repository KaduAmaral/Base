<?php
namespace Controller;

use \Core\Controller;
/**
* 
*/
class LoginController extends Controller
{
   
   function index() {
      $this->output = $this->load->content(
         '%header% %menu%'.
         '<div class="container"><div class="col-xs-12">'.
         'Nada implementado. <a href="'. $this->route->href() .'">Voltar</a>'.
         '</div></div>'.
         '%footer%',
         Array(
            'header' => $this->load->view('commons/head.phtml'),
            'menu'   => $this->load->view('commons/menu.phtml'),
            'footer' =>$this->load->view('commons/footer.phtml')
         )
      );
   }
} 