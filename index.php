<?php

session_start();
// ini_set("log_errors", 1);
// ini_set("error_log", "error.log");
// error_reporting(0);
// date_default_timezone_set('Europe/Istanbul');

DEFINE("ROOT", __DIR__);
DEFINE("SEP", DIRECTORY_SEPARATOR);
DEFINE("APP", ROOT . SEP . "app");
DEFINE("SYSTEM", ROOT . SEP . "system");
DEFINE("CONTROLLER", APP . SEP . "controller");
DEFINE("MODEL", APP . SEP . "model");
DEFINE("VIEW", APP . SEP . "view");
DEFINE("LAYOUT", APP . SEP . "layout");
DEFINE("APPINI", ROOT . SEP . 'app.ini');

spl_autoload_register(function ($className) {
    if (file_exists(SYSTEM . SEP . $className . ".php"))
        require_once SYSTEM . SEP . $className . ".php";
});

new app();