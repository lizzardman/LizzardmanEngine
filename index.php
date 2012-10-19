<?php

ini_set('display_errors', 'on');
error_reporting(E_ALL & ~E_NOTICE);

require_once "router.php";
require_once "view.php";
session_set_cookie_params(60 * 30, "/");
session_start();

//var_dump($_REQUEST);
Router::getInstance()->request($_REQUEST);


?>
