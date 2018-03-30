<?php
namespace Console\Commands;

class Build extends \CMP\Command\Command {

   private $console;
   private $config;
   private $app;
   private $appPath;

   public function execute(\CMP\Console $console, $args = []) {
      $this->console = $console;

      $this->setArgs($args);

      $this->build();

      $this->console->writeln('<success>Successful build!</success>');

      return TRUE;
   }

   private function setArgs($args) {
      $this->app = $args['--app'];
      $this->env = $args['--env'];
      $this->appPath = APPS.$this->app.DS;
   }

   public function getOptionCollection() {
      $collection = new \CMP\Command\OptionCollection();
      $collection->add('a|app:', 'Application folder name');
      $collection->add('e|env:?', 'Environment [prod|dev] (default: dev)', 'dev');

      return $collection;
   }

   private function build() {
     $setEnv = $this->console->getCommand('environment');
     $setEnv->execute($this->console, [
        '--env' => $this->env,
        '--app' => $this->app
     ]);

      $dumpRoutes = $this->console->getCommand('dump-routes');
      $dumpRoutes->execute($this->console, [
         '--app' => $this->app
      ]);


   }
}
