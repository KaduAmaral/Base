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

require_once ADDONS.'PHPMailer/PHPMailerAutoload.php';

use \PHPMailer;

class SendMail extends PHPMailer {
   

   private $vars;
   private $tplfolder;
   private $texto;
   private $debug = FALSE;
   private $config;

   function __construct($config = NULL) {

      if (!defined('EMAIL_CONFIG')){
         if (is_null($config))
            throw new \Exception('Configurações de e-mail não informada', 14);
         else
            $this->config = $config;
      } else
         $this->config = EMAIL_CONFIG;

      if ($this->config['debug'])
         $this->SMTPDebug  = 2; 

      if ($this->config['smtp'])
         $this->IsSMTP(); // telling the class to use SMTP
      
      if ($this->config['auth'])
         $this->SMTPAuth = TRUE; // enable SMTP authentication


      $this->SMTPSecure = $this->config['secure']; // sets the prefix to the servier
      
      $this->Host = $this->config['host'];

      $this->Port = 465; // set the SMTP port
      $this->Username = 'no-reply@joshuakingdom.com.br';
      $this->Password = 'iL]4*-64^$Ew';
      
      $this->isHTML(true);
      $this->setLanguage('pt');
      $this->CharSet = 'utf-8';
      $this->SetFrom($this->config['from']['email'], $this->config['from']['name']);

   }

   function Debug(){
      $this->debug = !$this->debug;
   }

   function load($file, $vars = []){
      return  Load::EMAIL($file, $vars);
   }

} 