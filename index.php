<?php
///////////////////////////////////////////////////////////////////////////////////
//  Main module
//
//  @package LizzardmanEngine 
//  @authors Artem Zelinskyi [lizzardman@], ...
//  @copyright 2012 
//  @version $Id$
//  @since 0.0.0
//  @license "THE BEER-WARE LICENSE"
//  
//  See license.txt for copyright notices and details.
/////////////////////////////////////////////////////////////////////////////////////

    
// Main engine defines   
ini_set('display_errors', 'on');
error_reporting(E_ALL & ~E_NOTICE);

require_once "router.php";
require_once "view.php";
session_set_cookie_params(60 * 30, "/");
session_start();


Router::getInstance()->request($_REQUEST);


?>
