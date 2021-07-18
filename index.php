<?
session_start();
DEFINE("ROOT", __DIR__);
DEFINE("APP", ROOT . "/app");
DEFINE("SYSTEM", ROOT . "/system");
DEFINE("CONTROLLER", APP . "/controller");
DEFINE("MODEL", APP . "/model");
DEFINE("VIEW", APP . "/view");

spl_autoload_register(function ($className) {
    if (file_exists(SYSTEM . "/" . $className . ".php"))
        require_once SYSTEM . "/" . $className . ".php";
});

new app();
