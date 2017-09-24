<?php
namespace Console\Commands;

class DumpRoutes extends \CMP\Command\Command {

   private $console;
   private $config;
   private $app;
   private $appPath;

   public function execute(\CMP\Console $console, $args = []) {
      $this->console = $console;

      $this->setArgs($args);

      $this->configure();

      $this->dumpRoutesCache();
   }


   private function setArgs($args) {
      $this->app = $args['--app'];
      $this->appPath = APPS.$this->app.DS;
   }

   private function dumpRoutesCache() {
         $this->console->writeln('Dumping routes...');
         $annotation = new \Core\Routes\Annotation();
         @unlink($this->appPath.'routes.cache.php');
         $annotation->dump();
   }

   private function configure() {
      if (empty($this->app) || !is_dir($this->appPath)) {
         throw new \Exception('Informe um app válido!');
         // $this->console->writeln('<error></error>');
         return false;
      }

      if (is_null($this->config = $this->console->share('config'))) {
        $this->config = $this->console->share('config', \Core\Config::SetApplication($this->appPath));
        $this->loadFile($this->appPath.'config.php');
      }

      return TRUE;
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

      return $collection;
   }


}
