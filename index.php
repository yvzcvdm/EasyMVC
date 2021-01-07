<?php

DEFINE("ROOT"       , __DIR__);
DEFINE("APP"        , ROOT."/app");
DEFINE("SYSTEM"     , ROOT."/system/");
DEFINE("CONTROLLER" , APP."/controller");
DEFINE("MODEL"      , APP."/model");
DEFINE("VIEW"       , APP."/view");


// function autoload($className)
// {
//     $className = ltrim($className, '\\');
//     $fileName  = '';
//     $namespace = '';
//     if ($lastNsPos = strrpos($className, '\\')) {
//         $namespace = substr($className, 0, $lastNsPos);
//         $className = substr($className, $lastNsPos + 1);
//         $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
//     }
//     $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

//     require SYSTEM.'/'.$fileName;
// }

function autoload( $class, $dir = null ) {

    if ( is_null( $dir ) )
      $dir = SYSTEM;

    foreach ( scandir( $dir ) as $file ) {

      // directory?
      if ( is_dir( $dir.$file ) && substr( $file, 0, 1 ) !== '.' )
        autoload( $class, $dir.$file.'/' );

      // php file?
      if ( substr( $file, 0, 2 ) !== '._' && preg_match( "/.php$/i" , $file ) ) {

        // filename matches class?
        if ( str_replace( '.php', '', $file ) == $class || str_replace( '.class.php', '', $file ) == $class ) {

            include $dir . $file;
        }
      }
    }
  }
spl_autoload_register('autoload');

new app();