<?php
namespace Core;

/**
* Config
*/
class Config {

   /**
    * Instância do objeto Config
    * @var Config
    */
   private static $instance;

   /**
    * Configurações padrão
    * @var array
    */
   protected $defaults = [];

   /**
    * Configurações setadas
    * @var array
    */
   protected $settings = [];

   /**
    * Idioma
    * @var string
    */
   protected $lang;

   /**
    * Diretório da aplicação
    * @var string
    */
   protected $dir;

   /**
    * Nome da aplicação
    * @var string
    */
   protected $name;

   /**
    * Diretório das Views
    * @var string
    */
   protected $views;

   /**
    * Diretório dos Models
    * @var string
    */
   protected $models;

   /**
    * Diretório dos Controllers
    * @var string
    */
   protected $controllers;

   /**
    * @deprecated
    * 
    * @var Mobile_Detect
    */
   protected $device;

   /**
    * @var array<string, mixin> Configurações de Email
    */
   protected $email;

   /**
    * Configurações de Autenticação
    * @var array
    */
   protected $authentication;

   /**
    * Configurações de Banco de Dados
    * @var array
    */
   protected $database;

   /**
    * @var array<string, mixin> Configuração do aplicativo
    */
   protected $appconfigs;

   /**
    * Usar sistema de rotas
    * @var bool
    *
    * @default TRUE
    */
   protected $useroutes = TRUE;

   /**
    * Não parsear URL caso não encontre a rota (url/controller/action)
    * @var bool
    */
   protected $onlyroutes;


   /**
    * Nome do diretório do aplicativo (caso esteja no diretório padrão /apps/<nome>/) 
    * ou caminho completo do diretório
    * 
    * Config constructor.
    * @param array $defaults
    */
   function __construct($defaults = []) {

      if (!empty(self::$instance))
         throw new \Core\Exception\InvalidApplicationException('A aplicação só pode ser definida uma vez!');

      $this->defaults($defaults);

      self::$instance = $this;

      self::Set();
   }

   /**
    * @param $app string
    * @return Config
    * @throws Exception\InvalidApplicationException
    */
   public static function SetApplication($app) {

      if (self::$instance instanceof self)
         throw new \Core\Exception\InvalidApplicationException('A aplicação só pode ser definida uma vez!');

      try {
         $dir = self::getAppDir($app);
      } catch (\InvalidArgumentException $e) {
         throw new \Core\Exception\InvalidApplicationException(
            'Aplicação inválida. Informe o nome ou caminho da aplicação que deseja executar '.
            'através do parâmetro '.__CLASS__.'::RUN("Nome|Diretorio").', 1, $e
         );
      }

      return New self([
         'name' => trim(str_replace(dirname($dir), '', $dir), '\/'),
         'dir'  => $dir
      ]);

   }


   /**
    * Seta as configurações
    * @param array $configs
    */
   public function Configure(array $configs) {
      //var_dump($configs);
      foreach ($configs as $config => $values) {
         if (is_callable([$this, $config]))
            $this->{$config}($values);
         else
            $this->appconfigs($config, $values);
      }
   }

   /**
    * Seta configurações
    * @param array $settings
    */
   public static function Set(array $settings = []) {
      if (empty($settings))
         $settings = self::$instance->defaults;
      self::$instance->settings = array_merge_recursive(self::$instance->settings, $settings);
      self::$instance->Configure($settings);

   }

   /**
    * Retornar diretório da aplicação
    * @param string $app
    * @return string
    */
   private static function getAppDir($app = NULL) {

      if (is_null($app) && !empty(self::$instance))
         $app = self::$instance->dir;

      if (is_null($app)) {
         throw new \InvalidArgumentException('Nenhuma aplicação foi informada para resgatar o diretório.');
      }

      $dir = rtrim($app, '\/') . DS;

      if (is_dir($dir))
         return $dir;
      else {

         $dir = APPS . trim($dir, '\/') . DS;

         if (is_dir($dir))
            return $dir;
         else
            throw new \InvalidArgumentException('Não foi possível identificar o diretório da aplicação pelo argumento: '.$app);
      }
   }

   /**
    * Seta propriedades padrões
    * @param array $defaults
    * @return array
    */
   private function defaults(array $defaults = []) {
      return $this->defaults = array_merge_recursive([
         'url' => $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'],
         'lang' => 'pt-br',
         'views' => $defaults['dir'].'view'.DS,
         'useroutes' => TRUE,
      ], $this->defaults, $defaults);
   }


   /* SET das Propriedades */


   /**
    * Seta configurações da aplicação
    * @param $prop
    * @param mixed $value
    * @param bool $setnull
    * @return mixed
    */
   public function appconfigs($prop, $value = NULL, $setnull = FALSE) {
      if (!is_array($this->appconfigs))
         $this->appconfigs = [];

      if (empty($prop))
         throw new \InvalidArgumentException('Informe o nome da propriedade.');

      if (is_null($value) && !$setnull) 
         return $this->appconfigs[$prop];

      $this->appconfigs[$prop] = objectify($value);
   }


   /**
    * Seta o valor da configuração: name
    * @param string $value - Novo valor da configuração
    * @return void
    */
   public function name($value) {
      if (empty($value))
         throw new \InvalidArgumentException('Não é possível setar a propriedade "name" para um valor vazio.');

      $this->name = $value;
   }

   /**
    * Seta o valor da configuração: dir
    * @param string $value - Novo valor da configuração
    * @return void
    */
   public function dir($value) {
      if (empty($value))
         throw new \InvalidArgumentException('Não é possível setar a propriedade "dir" para um valor vazio.');

      if (!empty($this->dir))
         throw new \Core\Exception\ImmutablePropertyException('A propriedade "dir" não pode ser alterada.');
         
      $this->dir = $value;

      if (!defined('APP'))
         define('APP', $this->dir);
   }

   /**
    * Seta o valor da configuração: url
    * @param string $value - Novo valor da configuração
    * @return void
    */
   public function url($value) {
      if (empty($value))
         throw new \InvalidArgumentException('Não é possível setar a propriedade "url" para um valor vazio.');

      $this->url = $value;
   }

   /**
    * Seta o valor da configuração: lang
    * @param string $value - Novo valor da configuração. O formato deve ser {xx-xx}
    * @return void
    */
   public function lang($value){
      if (empty($value))
         throw new \InvalidArgumentException('Não é possível setar a propriedade "lang" para um valor vazio.');

      if ( preg_match('/^([a-z]{2}-[a-z]{2})$/', strtolower($value)) === 0 )
         throw new \InvalidArgumentException(
            'Formato inválido. Forneça um idioma no padrão "ii-pp", onde {ii} representa o idioma com 2 caracteres e '.
            '{pp} o país com 2 caracteres. Por exemplo: pt-br'
         );

      $this->lang = $value;
   }

   public function views($value) {
      $this->views = $value;
   }

   public function models($value) {
      $this->models = $value;
   }


   public function controllers($value) {
      $this->controllers = $value;
   }

   public function authentication($value) {
      if (empty($value))
         throw new \InvalidArgumentException('Não é possível setar a propriedade "authentication" para um valor vazio.');

      $this->authentication = objectify($value);
   }

   public function database($value) {
      $this->database = objectify($value);
   }

   public function useroutes($value) {
      $this->useroutes = $value;
   }

   public function onlyroutes($value) {
      $this->onlyroutes = $value;
   }


   public function email($value) {
      if (empty($value))
         throw new \InvalidArgumentException('Não é possível setar a propriedade "email" para um valor vazio.');

      $this->email = objectify($value);
   }

   public function __get($key) {
      if (property_exists($this, $key)) {

         if (empty($this->{$key}) && !empty($this->defaults[$key]))
            return $this->defaults[$key];
         else
            return $this->{$key};
      } else if (isset($this->appconfigs[$key]))
        return $this->appconfigs($key);

      throw new \Core\Exception\InvalidPropertyException("A propriedade '{$key}' é inválida.");
   }

   public function __isset($key) {
      if (isset($this->{$key})) {
         return (FALSE === empty($this->{$key}));
      } else if (isset($this->appconfigs[$key])) {
         return (FALSE === empty($this->appconfigs[$key]));   
      } else {
         return NULL;
      }
   }

   public static function getInstance() {

      if ( !(self::$instance instanceof self) )
         self::$instance = New self();

      return self::$instance;
   }

} 