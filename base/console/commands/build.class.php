<?php
namespace Console\Commands;

class Build extends \CMP\Command\Command {

   private $console;
   private $config;
   private $app;
   private $appPath;

   public function execute(\CMP\Console $console, $args = []) {
      $this->console = $console;
      
      try {
         $this->setArgs($args);
         
         $this->copyConfig($args['--env']);

         $this->configure();
   
         $this->dumpRoutesCache();
      } catch (\Exception $e) {
         $this->console->writeln("<error>Exception: {$e->getMessage()}</error>");
      }
      

      return TRUE;
   }

   private function setArgs($args) {
      $this->app = $args['--app'];
      $this->appPath = APPS.$this->app.DS;
   }

   private function configure() {
      if (empty($this->app) || !is_dir($this->appPath)) {
         throw new \Exception('Informe um app válido!');
         // $this->console->writeln('<error></error>');
         return false;
      }

      // try {
         $this->config = \Core\Config::SetApplication($this->app);
      // } catch (InvalidApplicationException $e) {
      //    $this->console->writeln('<error>'.$e->getMessage().'</error>');
      //    return FALSE;
      // }

      $this->loadFile($this->appPath.'config.php');

      return TRUE;
   }

   private function dumpRoutesCache() {
      // try {
         $this->console->writeln('<info>Dumping routes...</info>');
         $annotation = new \Core\Routes\Annotation();
         @unlink($this->appPath.'routes.cache.php');
         $annotation->dump();
         $this->console->writeln('<success>Routes dumped with success!</success>');
      // } catch (\Exception $e) {
         // $this->console->writeln("<error>{$e->getMessage()}</error>");
      // }
   }

   private function copyConfig($env) {
      if (file_exists($this->appPath.'config.php.'.$env))
         copy($this->appPath.'config.php.'.$env, $this->appPath.'config.php');
      else
         throw new \Exception('Config file does\'t exists for environment '.$env);
   }

   private function loadFile($file) {
      if (file_exists($file))
         require $file;
      else 
         throw new \Exception("Can't load file: {$file}");
   }

   public function getOptionCollection() {
      $collection = new \CMP\Command\OptionCollection();
      $collection->add('a|app:', 'Application folder name');
      $collection->add('env:?', 'Environment [prod|dev] (default: dev)', 'dev');

      return $collection;
   }
}