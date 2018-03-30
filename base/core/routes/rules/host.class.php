<?php
namespace Core\Routes\Rules;

use \Core\Request;
use \Core\Routes\Route;

class Host implements RuleInterface {
   /**
    *
    * Rota
    *
    * @var Route
    *
    */
   protected $route;
   /**
    *
    * Expressão Regular
    *
    * @var string
    *
    */
   protected $regex;


   /**
    *
    * Checa a requisição, e verifica os atributos
    *
    * @param Request $request Objeto da Requisição
    *
    * @param Route $route Rota
    *
    * @return bool TRUE em sucesso, FALSE em falha
    *
    */
   public function __invoke(Request $request, Route $route) {

      // var_dump($route->host);

      if (!$route->host)
         return true;

      $match = preg_match(
         $this->buildRegex($route),
         $request->uri,
         $matches
      );

//      if ($this->route->host != '/404') {
//         echo '<div style="background-color:white;padding:15px;margin:10px;font-family:monospace;">';
//         echo 'BASE: ' . $this->route->base . ' <br>' . PHP_EOL;
//         echo 'HOST: ' . $this->route->host . ' <br>' . PHP_EOL;
//         echo 'REGX: ' . $this->regex . ' <br>' . PHP_EOL;
//         echo 'URI : ' . $request->uri . ' <br>' . PHP_EOL;
//         var_dump($matches);
//         echo "</div>";
////         exit;
//      }

      if (!$match)
         return FALSE;


      $route->attributes($this->getAttributes($matches));
      return TRUE;
   }
   /**
    *
    * Gets the attributes out of the regex matches.
    *
    * @param array $matches The regex matches.
    *
    * @return array
    *
    */
   protected function getAttributes($matches) {

      $attributes = [];
      foreach ($matches as $key => $val) {
         if (is_string($key)) {
            $attributes[$key] = $val;
         }
      }
      return $attributes;
   }
   /**
    *
    * Builds the regular expression for the route host.
    *
    * @param Route $route The Route.
    *
    * @return string
    *
    */
   protected function buildRegex(Route $route) {
      $this->route = $route;
      $this->regex = $this->setParams( str_replace('.', '\\.', $this->route->host) );

      $this->regex = '#^' . $route->base . '/' . trim($this->regex, '/') . '$#';
      return $this->regex;
   }

   protected function setParams($host) {

      if ( count($this->route->params) > 0 ) {
         foreach ($this->route->params as $param => $ex) {
            // echo $host . PHP_EOL;
            // echo $param .'  ::  '. $ex . PHP_EOL;
            // echo '(?<' . $param . '>' . trim($ex,'()') . ')' . PHP_EOL.PHP_EOL;

            $host = str_replace(":{$param}", "(?<{$param}>".trim($ex,'()').')', $host);
         }
         
      }

      return $host;
   }


}