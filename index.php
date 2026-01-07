<?php
// Session cookie ayarlarını yapılandır
session_set_cookie_params([
    'lifetime' => 86400 * 30,  // 30 gün
    'path' => '/',
    'domain' => '',
    'secure' => false,          // HTTP için false, HTTPS için true
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

ini_set("log_errors", 1);
ini_set("error_log", "error.log");
error_reporting(E_ALL);
ini_set("display_errors", 1);
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
    static $class_cache = [];
    
    // Cache'de varsa direkt yükle
    if (isset($class_cache[$className])) {
        require_once $class_cache[$className];
        return;
    }
    
    // Sırasıyla kontrol et ve cache'e kaydet
    $paths = [
        CORE . SEP . $className . ".php",
        CONTROLLER . SEP . $className . ".php",
        MODEL . SEP . $className . ".php"
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            $class_cache[$className] = $path;
            require_once $path;
            return;
        }
    }
});

new app();
