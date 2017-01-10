<?php

use \Core\Routes\Router;

// Seta a rota de erro 404
Router::notfound([
    'controller' => '\Controller\ErrorController',
    'action' => 'index'
]);

// Rota: GET /post/<id>
Router::get('/post/:nome/:id',[
   'controller' => '\Controller\MainController',
   'action' => 'teste'
])->params([
   'id' => '\d+',
   'nome' => '\w+'
]);

// Rota: GET /post/<algum-endereco>
Router::get('/post/:slug',[
    'controller' => '\Controller\MainController',
    'action' => 'action'
])->params([
    'slug' => '[a-zA-Z0-9\-_]+'
]);