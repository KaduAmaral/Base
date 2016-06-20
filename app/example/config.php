<?php

use \Core\Routes\Router;
use \Core\Config;

Config::Set([
   'url' => 'http://localhost/Base/public_html/',
   'name' => 'example',
]);

Router::notfound([
   'controller' => 'Error',
   'action' => 'index'
]);

Router::main([
   'controller' => 'Main',
   'action'     => 'index'
]);

Router::route('/index', Router::main());

Router::route('/entrar',[
   'controller' => 'Login',
   'action' => 'index'
]);

Router::route('/post/:id',[
   'controller' => 'Main',
   'action' => 'teste'
])->params([
      'id' => '\d+'
]);

Router::route('/post/:slug',[
   'controller' => 'Main',
   'action' => 'action'
])->params([
   'slug' => '[a-zA-Z0-9\-_]+'
]);


Router::route('/post/:date/:slug', [
   'controller' => 'Main',
   'action' => 'pdate'
])->params([
   'date' => '[0-9]{2}-[0-9]{2}-[0-9]{4}',
   'slug' => '[a-zA-Z0-9\-_]+'
]);