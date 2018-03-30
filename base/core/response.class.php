<?php

namespace Core;

class Response {

    /**
    * @var \Core\Response
    */
   static private $instance;
    
    protected $response_code;
    protected $body;
    protected $headers;

    public function __construct($body = '', $http_response_code = 200) {
        $this->body = $body;
        $this->response_code = $http_response_code;
        $this->headers = [];
    }

    public function setResponseCode($code) {
        $this->response_code = $code;
        return $this;
    }

    public function setBody($body) {
        $this->body = $body;
        return $this;
    }

    public function body() {
        return $this->body;
    }

    public function setJSON($obj) {
        $this->setHeader('Content-Type: application/json');
        $this->body = \json_encode($obj);
        return $this;
    }

    public function setHeader($header) {
        $this->headers[] = $header;
        return $this;
    }

    public function raw($echo = TRUE) {
        http_response_code($this->response_code);
        foreach ($this->headers as $header) {
            \header($header);
        }

        if ($echo)
            echo $this->body;
        else 
            return $this->body;

    }

    /**
    * 
    * Retorna a instância da requisição
    * 
    * @return Request
    * 
    */
   public static function getInstance() {
    
        if ( !(self::$instance instanceof self) )
            self::$instance = New self();

        return self::$instance;
    }

}
