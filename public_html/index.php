<?php
   // Define qual aplicação será executada.
   define('APP', 'example');

   require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'loader.php';

   \Core\Application::RUN();