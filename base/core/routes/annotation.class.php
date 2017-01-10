<?php
namespace core\routes;


use Core\Config;

class Annotation {

   /**
    * @var \ReflectionClass
    */
   private $reflection;

   private $classRoute;

   private $actionsRoutes = [];

   private $config;

   private $cache = [];

   public function __construct($className = NULL) {
      if (!is_null($className)) $this->go($className);

      $this->config = Config::getInstance();


      try {
         if (!file_exists($this->config->dir . 'routes.cache.php'))
            $this->dump();
      } catch (\Exception $e) {
         throw $e;
      }

      $this->loadAnnotationsCache();
   }

   private function loadAnnotationsCache() {
      $file = $this->config->dir . 'routes.cache.php';
      if (file_exists($file) && is_readable($file))
      include_once $file;
   }

   public function go($className) {
      try {
         $this->reflect($className);
         $this->parse();
      } catch (\Exception $e) {
         throw $e;
      }

   }

   public function dump() {
      $controllers = rtrim($this->config->controllers  ?: ($this->config->dir . 'controller'), '/ ') . '/';

      if (!is_dir($controllers))
         throw new \InvalidArgumentException('O diretório de Controllers não foi configurado corretamente.');

      $files = scandir($controllers);


      foreach ($files as $file) {

         try {
            if (strpos($file, '.') != 0 && !is_dir($file) && is_file($controllers.$file))
               $this->go($this->getClassNameFromFile($controllers.$file));
         } catch (\Exception $e) {
            throw $e;
         }
      }

      $this->writeCacheFile();

   }

   private function getClassNameFromFile($file) {
      $fp = fopen($file, 'r');
      $class = $namespace = $buffer = '';
      $i = 0;

      while (!$class) {
         if (feof($fp)) break;

         $buffer .= fread($fp, 512);
         $tokens = @token_get_all($buffer);

         if (strpos($buffer, '{') === false) continue;

         for (;$i<count($tokens);$i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
               for ($j=$i+1;$j<count($tokens); $j++) {
                  if ($tokens[$j][0] === T_STRING) {
                     $namespace .= '\\'.$tokens[$j][1];
                  } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                     break;
                  }
               }
            }

            if ($tokens[$i][0] === T_CLASS) {
               for ($j=$i+1;$j<count($tokens);$j++) {
                  if ($tokens[$j] === '{') {
                     $class = $tokens[$i+2][1];
                  }
               }
            }
         }
      }

      return $namespace.'\\'.$class;
   }

   public function writeCacheFile() {
      $file = $this->config->dir . 'routes.cache.php';

      $head = '<?php '.PHP_EOL.'use \Core\Routes\Router;'.PHP_EOL.PHP_EOL;
      $content = '';
      foreach ($this->cache as $controller => $actionsRoutes) {
         $routes = $this->getRoutes($controller, $actionsRoutes);

         foreach ($routes as $route) {
            if (!empty($route))
               $content .= 'Router::route('.var_export($route, true).');'.PHP_EOL.PHP_EOL;
         }

      }

      if (!empty($content))
         file_put_contents($file, $head.$content);


   }

   public function reflect($className) {
      $this->reflection = NULL;
      $this->classRoute = NULL;
      $this->actionsRoutes = [];
      $this->reflection = new \ReflectionClass($className);

      $this->cache[$this->reflection->getName()] = [];
   }

   public function parse() {

      try {
         $this->classRoute = $this->parseDoc($this->reflection->getDocComment() );
      } catch (\InvalidArgumentException $e) {
         throw new \InvalidArgumentException('Anotação Inválida da classe '.$this->reflection->getName(), 530, $e);
      }



      $methods = $this->reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

      foreach ($methods as $method) {
         try {
            $route = $this->parseDoc($method->getDocComment());
         } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException('Anotação Inválida do método '.$this->reflection->getName().'::'.$method->getName(), 530, $e);
         }


         if ($route)
            $this->actionsRoutes[$method->getName()] = $route;
      }

      $this->cache[$this->reflection->getName()] = $this->actionsRoutes;
   }

   public function parseDoc($docblock) {
      if (empty($docblock))
         return FALSE;

      $doc = trim($docblock, "\n*");
      $doc = explode("\n", $doc);
      foreach ($doc as $line) {
         $line = trim($line, ' /*');
         $match = preg_match('/\@Route(\s+)?\(\"?([a-zA-Z\:\/]+)\"(.*)?\)/', $line, $matches );
         if (0 < $match)
            break;
      }

      if (is_null($matches))
         return FALSE;

      if (empty($matches[2]))
         return FALSE;

      $path = $matches[2];

      $params = NULL;
      try {
         if (!empty($matches[3]))
            $params = $this->parseParams($matches[3]);
      } catch (\InvalidArgumentException $e) {
         throw new \InvalidArgumentException('A anotação de rota é inválida: '.$line, 530, $e);
      }


      return array_merge([
         'host' => $path
      ], (array) $params);

   }
   
   private function parseParams($string) {
      $string = str_replace('\\', '\\\\', trim($string, ' ,'));

      if (empty($string))
         return NULL;

      $json = json_decode('{'.$string.'}', true);

      if (is_null($json))
         throw new \InvalidArgumentException('Os parâmetros informados é inválido.');

      return $json;

      $matches = NULL;
      $match = preg_match_all('/(\w+\=["{][a-zA-Z0-9_\-"\+\\\\\= ,\|]+[}"])/', $string, $matches);

      $params = NULL;


      if (!$match)
         return NULL;

      $params = $matches[0];

      $parameters = [];
      foreach ($params as $param) {
         $key = trim(substr($param, 0, strpos($param, '=') ));
         $value = trim(substr($param, strpos($param, '=') + 1), ' "');

         if (strpos($value, '{') === 0) {
            $value = $this->parseParams( trim($value, ' {}') );
         }


         $parameters[$key] = $value;

      }

      return $parameters;
   }

   public function getRoutes($controller, $actionsRoutes) {
      $routes = [];

      foreach ($actionsRoutes as $method => $actionRoute) {

         $routes[] = array_merge($actionRoute, [
            'controller' => $controller,
            'action' => $method
         ]);
      }

      return $routes;
   }

}