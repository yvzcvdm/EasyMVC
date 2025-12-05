<?php
session_start();
ini_set("log_errors", 1);
ini_set("error_log", "error.log");
error_reporting(E_ALL);
ini_set("display_errors", 1);  // Bu satırı ekleyin
date_default_timezone_set('Europe/Istanbul');

DEFINE("ROOT", __DIR__);
DEFINE("SEP", DIRECTORY_SEPARATOR);
DEFINE("APP", ROOT . SEP . "app");
DEFINE("CORE", ROOT . SEP . "core");
DEFINE("CONTROLLER", APP . SEP . "controller");
DEFINE("MODEL", APP . SEP . "model");
DEFINE("VIEW", APP . SEP . "view");
DEFINE("LAYOUT", APP . SEP . "layout");
DEFINE("APPINI", ROOT . SEP . 'app.ini');

spl_autoload_register(function ($className) {
    if (file_exists(CORE . SEP . $className . ".php"))
        require_once CORE . SEP . $className . ".php";
});

new app();
