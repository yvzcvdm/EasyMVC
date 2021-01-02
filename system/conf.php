<?php
ini_set('session.cookie_domain', substr($_SERVER['SERVER_NAME'], strpos($_SERVER['SERVER_NAME'], "."), 100));
if (ob_start("ob_gzhandler")) ob_start();
session_start();
date_default_timezone_set('Europe/Istanbul');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);