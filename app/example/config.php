<?php

use \Core\Routes\Router;
use \Core\Config;

/**
 * Portugues:
 *
 * Configura a aplicação, setando o name e URL
 * Outras configurações também são possíveis, como conexão com banco,
 * Configurações de e-mail e etc.
 *
 *
 * English:
 *
 * Set the application by setting the name and URL
 * Other settings are also posible like connection to the bank,
 * E-mail settings, etc.
 **/


Config::Set([
   'url' => 'http://localhost/Base/public_html/',
   'name' => 'example',
]);

// Seta a rota de erro 404
Router::notfound([
   'controller' => 'Error',
   'action' => 'index'
]);


// Setá a rota padrão, home/index
Router::main([
   'controller' => 'Main',
   'action'     => 'index'
]);

// Apenas replicando a rota  padrão para ser acessada através do endereço /index
Router::route('/index', Router::main());

// Rota para o formulário de LOGIN, acessível apenas via GET
Router::get('/entrar', 'entrar', [
   'controller' => 'Login',
   'action' => 'index'
]);

// Replicando a rota /entrar para /login. Permitira acesso pelo mesmo Method que a rota /entrar (GET)
Router::route('/login', 'login.form', Router::GetByName('entrar')->_clone());

// Rota para realizar o login, acessível apenas via POST
Router::post('/login', 'login', [
   'controller' => 'Login',
   'action' => 'login'
]);


// Rota: GET /post/<id>
Router::get('/post/:id',[
   'controller' => 'Main',
   'action' => 'teste'
])->params([
      'id' => '\d+'
]);

// Rota: GET /post/<algum-endereco>
Router::get('/post/:slug',[
   'controller' => 'Main',
   'action' => 'action'
])->params([
   'slug' => '[a-zA-Z0-9\-_]+'
]);


// Rota: GET /post/<DD-MM-YYYY>/<algum-endereco>
Router::get('/post/:date/:slug', [
   'controller' => 'Main',
   'action' => 'pdate'
])->params([
   'date' => '[0-9]{2}-[0-9]{2}-[0-9]{4}',
   'slug' => '[a-zA-Z0-9\-_]+'
]);