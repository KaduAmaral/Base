<?php

//require_once CORE . 'exception' . DS . 'systemexception.class.php';


$autoloadlog = '';


function search_lib($lib, $file, $ds = '/'){
    // Verifica se o diretório informado é válido
    global $autoloadlog;

    if (is_array($lib)){
         foreach ($lib as $dir) {
             if ($f = search_lib($dir, $file, $ds)){
                  return $f;
                  break;
             }
         }
         return FALSE;
    }

    if (is_dir($lib)){
         $path = isset($path) ? $path : "";
         $path = cleanPath($lib, $path, $ds);

         $autoloadlog .= 'Lib: ' . (is_array($lib) ? implode(', ', $lib) : $lib) . PHP_EOL . 
                                 'File: ' . $file . PHP_EOL . PHP_EOL . 
                                 'Path: ' . $path . PHP_EOL . PHP_EOL . 
                                 (file_exists($path) ? 'EXISTS!' : 'NOT Exists!') . PHP_EOL . PHP_EOL . 
                                 str_repeat('-', 200) . PHP_EOL . PHP_EOL;


         // Verifica se o arquivo já existe neste primeiro diretório
         if (file_exists($path)) 
             return $path;

         // Lista os subdiretórios e arquivos
         $dirs = array_diff(scandir($lib, 1), ['.','..']);
         foreach ($dirs as $dir) {

             // Verifica se é um arquivo se for, pula para o próximo
             if (!is_dir($lib.$ds.$dir)) continue;

             // Se for um diretório procura dentro dele
             $f = search_lib($lib.$ds.$dir, $file, $ds);

             // Caso não encontre retora FALSE
             if ($f !== FALSE) return $f;
         }

    } else
         $autoloadlog .= 'Lib "'. $lib .'" is not a dir.'.PHP_EOL.PHP_EOL;

    // Se o diretório informado não for válido ou se não tiver encontrado retorna FALSE
    return FALSE;
}


function cleanPath($lib, $file, $ds = '/') {
    $lib = rtrim($lib, '/\\');

    $path = strtolower($lib.$ds.$file);
    $path = str_replace(['\\', '/'], $ds, $path);

    return $path;
}

spl_autoload_register(
    function ($class){
         global $autoloadlog;

         $libs = [BASE];

         if (defined('APP'))
            $libs[] = APP;

         $ext  = '.class.php';
         $debug = !TRUE;

         $file = FALSE;

         $autoloadlog .= '<h3>'.$class.'</h3>';

         foreach ($libs as $lib) {
             $path = cleanPath($lib, $class.$ext, DIRECTORY_SEPARATOR);

             $autoloadlog .= '<pre>Lib: ' . (is_array($lib) ? implode(', ', $lib) : $lib) . PHP_EOL . 
                            'File: ' . $class.$ext . PHP_EOL . PHP_EOL . 
                            'Path: ' . $path . PHP_EOL . PHP_EOL . 
                            (file_exists($lib.$class.$ext) ? 'EXISTS!' : 'NOT exists!') . PHP_EOL . 
                            'BackTrace: ' . var_export(debug_backtrace(), true) . PHP_EOL . PHP_EOL . 
                            str_repeat('-', 200) . PHP_EOL . PHP_EOL.'</pre>';


             if (file_exists($path)){
                  $file = $path;
                  break;
             }
         }

         //$file = search_lib($libs, $class.$ext);

         // Debug
         if ($debug) echo $autoloadlog;

         // Se encontrou inclui o arquivo
         if ($file !== FALSE  && is_string($file) && $file !== '') {
             
             require_once $file;

             if (!class_exists($class, FALSE)){
                //trigger_error('Autoload error: File loaded, but class not found.' , E_USER_ERROR);
                //throw new \Core\Exception\SystemException(\Core\Exception\Exceptions::E_CLASSNOTEXIST, [$class]);
                //throw new Exception("Autoload error: File loaded, but class '{$class}' not found.");
             }

         } else { // Se não encontrar o arquivo lança um erro na tela. :)

             if (is_array($libs)) $libs = implode($class.$ext . '</code>, <code>', $libs);

             //trigger_error("Autoload error: Can't find the file {$class}{$ext} on [{$libs}]!" , E_USER_ERROR);
             //throw new \Core\Exception\SystemException(\Core\Exception\Exceptions::E_FILENOTFOUND, ["<code>{$libs}".$class.$ext."</code>"]);
             //throw new Exception("Autoload error: Can't find the file {$class}{$ext} on [{$libs}]!");
         }

    }
); 