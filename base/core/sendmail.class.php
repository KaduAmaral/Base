<?php
/**
* Notificações e envio de emails
* Utilize as váriaves no template da seguinte forma: {ORDEM-E-PROGRESSO}
* E registre as variáveis assim: $sendmail->SetVars(array('ORDEM-E-PROGRESSO' => 'Bandeira Brasileira'));
* Para um template dentro do outro use: 
*
*
*   $vars = Array(
*      'VAR1'  => 'Valor 1', // Variaveis comuns (do primeiro template)
*      'VAR2'  => 'Valor 2', // Variaveis comuns (do primeiro template)
*      '<TPL>' => array('VAR3' 'nome-do-template', $array_de_variaveis_do_segundo_template) // Isso pode ser recursivo...
*   );
*
*/
namespace Core;

class SendMail extends \PHPMailer {

   private $vars;
   private $tplfolder;
   private $texto;
   private $debug = FALSE;
   private $config;

   function __construct() {
      parent::__construct();

      if (empty(Config::getInstance()->email))
         throw new \Exception('Configurações de e-mail não informada', 14);
      else
         $this->config = Config::getInstance()->email;

      if (!empty($this->config->debug) && $this->config->debug)
         $this->SMTPDebug  = 2; 

      if (!empty($this->config->smtp) && $this->config->smtp)
         $this->IsSMTP(); // telling the class to use SMTP
      
      if (!empty($this->config->auth) && $this->config->auth)
         $this->SMTPAuth = TRUE; // enable SMTP authentication

      if (!empty($this->config->secure))
         $this->SMTPSecure = $this->config->secure; // sets the prefix to the servier
      
      if (!empty($this->config->host))
         $this->Host = $this->config->host;

      if (!empty($this->config->port))
         $this->Port = $this->config->port; // set the SMTP port || 465
      
      $this->Username = $this->config->user;
      $this->Password = $this->config->pass;
      
      $this->isHTML(true);

      $this->setLanguage('pt');

      $this->CharSet = 'utf-8';

      if (!empty($this->config->from))
         $this->SetFrom($this->config->from->email, !empty($this->config->from->name) ? $this->config->from->name : $this->config->from->email);

   }

   function Debug() {
      $this->debug = !$this->debug;
   }

}