<?php
namespace Console\Commands;

class Environment extends \CMP\Command\Command {

   private $console;
   private $config;
   private $env;
   private $app;
   private $appPath;

   public function execute(\CMP\Console $console, $args = []) {
      $this->console = $console;

      $this->setArgs($args);

      $this->console->writeln('Setting environment: '.$this->env);

      $this->copyConfig();

      $this->configure();

      $this->copyHtaccess();

      return TRUE;
   }

   private function setArgs($args) {
      $this->app = $args['--app'];
      $this->env = $args['--env'];
      $this->appPath = APPS.$this->app.DS;

   }

   private function copyConfig() {
     $this->console->writeln('Setting config file...');
     $path = "{$this->appPath}config.{$this->env}.php";
      if (file_exists($path))
         copy($path, "{$this->appPath}config.php");
      else
         throw new \Exception('Config file does\'t exists for environment '.$this->env);
   }

   private function copyHtaccess() {
     $path = "{$this->config->public}/{$this->env}.htaccess";
     $newPath = "{$this->config->public}/.htaccess";
     $this->console->writeln("Setting htaccess file");
     $this->console->writeln("From: <info>{$path}</info>");
     $this->console->writeln("To: <info>{$newPath}</info>");
     if (file_exists($path))
        copy($path, $newPath);
     else
        throw new \Exception('HTACCESS file does\'t exists for environment '.$this->env);
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
      $collection->add('e|env:', 'Environment [prod|dev] (default: dev)', 'dev');

      return $collection;
   }

}
