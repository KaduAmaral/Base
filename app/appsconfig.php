<?php

return objectify(Array(
   'example' => Array(
      'url'          => URL,
      'languages'    => ['pt-br'], // Idiomas Disponíveis
      'language'     => 'pt-br',   // Idioma Padrão
      'view'         => APPS . 'example' . DS . 'view' . DS,
      'database'     => Array(
         'drive'  => 'mysql',
         'port'   => 3306,
         'host'   => 'localhost',
         'user'   => 'root',
         'pass'   => ''
      ),
      'authentication' => FALSE
   )
)); 