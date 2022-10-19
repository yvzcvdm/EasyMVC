<?php
DEFINE("ROOT", __DIR__);
DEFINE("SEP", DIRECTORY_SEPARATOR);
DEFINE("APP", ROOT . SEP . "app");
DEFINE("SYSTEM", ROOT . SEP . "system");
DEFINE("CONTROLLER", APP . SEP . "controller");
DEFINE("MODEL", APP . SEP . "model");
DEFINE("VIEW", APP . SEP . "view");
DEFINE("APPINI", ROOT . SEP . 'app.ini');

spl_autoload_register(function ($className) {
    if (file_exists(SYSTEM . SEP . $className . ".php"))
        require_once SYSTEM .SEP . $className . ".php";
});

new app();
