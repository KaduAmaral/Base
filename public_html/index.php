<?php

   error_reporting(E_ALL);
   ini_set('display_errors', 1);

   require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'loader.php';

   \Core\Application::RUN('example');