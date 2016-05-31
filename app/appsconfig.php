<?php

return objectify(Array(
   'example' => Array(
      'url' => 'http://localhost/Base/public_html/',
      'database'     => Array(
         'driver'  => 'mysql',
         'port'   => 3306,
         'host'   => 'localhost',
         'user'   => 'root',
         'pass'   => '',
         'schema' => 'test;charset=utf8'
      )
   )
)); 