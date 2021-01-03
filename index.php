<?php

DEFINE("ROOT"       , __DIR__);
DEFINE("APP"        , ROOT."/app");
DEFINE("SYSTEM"     , ROOT."/system");
DEFINE("CONTROLLER" , APP."/controller");
DEFINE("MODEL"      , APP."/model");
DEFINE("VIEW"       , APP."/view");

include_once(SYSTEM . '/mail.php');
include_once(SYSTEM . '/db.php');
include_once(SYSTEM . '/init.php');
include_once(SYSTEM . '/view.php');
include_once(SYSTEM . '/app.php');

new app();