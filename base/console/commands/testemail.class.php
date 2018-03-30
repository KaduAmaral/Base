<?php
namespace Console\Commands;

class TestEmail extends \CMP\Command\Command {

   private $console;
   private $config;
   private $app;
   private $email;
   private $debug;
   private $appPath;

   public function execute(\CMP\Console $console, $args = []) {
      $this->console = $console;

      $this->setArgs($args);

      $this->configure();

      $this->sendEmail();

      return TRUE;
   }

   private function sendEmail() {
      $this->console->writeln('Testing email for: '.$this->app);

      $mail = new \Core\SendMail();

      $mail->addAddress($this->email);

      $this->SMTPDebug = $this->debug == 'y' ? 2 : 0; 


      $mail->Subject = 'Testing Sendmail for '.$this->app;
      $mail->msgHTML('<p>Hello, your e-mail config is setted correct!</p>');
      if (!$mail->send())
         $this->console->writeln("Mailer Error: <error>{$mail->ErrorInfo}</error>");
     else
         $this->console->writeln("<success>Mailer successful sended!</success>");

   }

   private function setArgs($args) {
      $this->app = $args['--app'];
      $this->email = $args['--email'];
      $this->debug = $args['--debug'];
      $this->appPath = APPS.$this->app.DS;
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
      $collection->add('e|email:', 'Destinatary e-mail');
      $collection->add('d|debug:?', 'Debug PHPMailer [y|n]', 'n');

      return $collection;
   }

}
