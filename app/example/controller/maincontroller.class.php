<?php
namespace Controller;

use \Core\Controller;

/**
* Main Controller
*/
class MainController extends Controller {

   /**
    * @Route("/", {"name":"index", "allows":"GET|POST"})
    * @return mixed
    */
   public function index(){
      return $this->load->view('pages/index');
   }

   /**
    *
    * @Route("/post/:id", {"params":{"id":"\d+"}})
    *
    * @param $id
    * @param string $nome
    * @return string
    */
   public function teste($id, $nome = 'Fulano') {
      return "Olá $nome, o ID é {$id}";
   }


   /**
    * @Route("/acao/:slug", {"name":"acao", "params":{"slug":"[a-zA-Z0-9\-_]"}})
    * 
    * @param $slug
    * @return string
    */
   public function action($slug) {
      return "Post: {$slug}";
   }

   /**
    * @Route("/post/:date/:slug", {"params":{"date":"[0-9]{2}-[0-9]{2}-[0-9]{4}", "slug":"[a-zA-Z0-9\-_]"}})
    *
    * @param $date
    * @param $slug
    * @return string
    */
   public function pdate($date, $slug) {
      return "Postagem: {$slug}<br>Data: {$date}";
   }
} 