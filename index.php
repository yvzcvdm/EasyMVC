<?php
session_start();

// ini_set('display_errors', 0);
// if (version_compare(PHP_VERSION, '5.3', '>=')) {
//     error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
// } else {
//     error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
// }


DEFINE("ROOT", __DIR__);
DEFINE("APP", ROOT . "/app");
DEFINE("SYSTEM", ROOT . "/system");
DEFINE("CONTROLLER", APP . "/controller");
DEFINE("MODEL", APP . "/model");
DEFINE("VIEW", APP . "/view");

include(SYSTEM.'/config.php');

spl_autoload_register(function ($className) {
    if (file_exists(SYSTEM . "/" . $className . ".php"))
        require_once SYSTEM . "/" . $className . ".php";
});

new app($config);