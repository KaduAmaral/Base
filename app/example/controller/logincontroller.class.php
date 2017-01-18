<?php
namespace Controller;

use \Core\Controller;


class LoginController extends Controller
{

   /**
    * @Route("/entrar", {"name":"entrar"})
    */
   function index() {
      return $this->load->view('layouts/default', [
         'content' => '<div class="container"><div class="col-xs-12 col-sm-6 col-sm-offset-3">'.
            '<div class="page-header"><h1>Entrar</h1></div>'.
            $this->load->view('commons/login-form').
            '</div></div>'
      ]);
   }

   /**
    * @Route("/login", {"name":"login"})
    */
   function login() {
      return $this->load->content(
         '%header% %menu%'.
         '<div class="container"><div class="col-xs-12 col-sm-6 col-sm-offset-3">'.
         '<div class="page-header"><h1>Dados do Login</h1></div>'.
         '<pre class="well">%login%</pre>'.
         '</div></div>'.
         '%footer%',
         Array(
            'header' => $this->load->view('commons/head'),
            'menu'   => $this->load->view('commons/menu'),
            'footer' =>$this->load->view('commons/footer'),
            'login'  => var_export($this->request->post, TRUE)
         )
      );
   }
}