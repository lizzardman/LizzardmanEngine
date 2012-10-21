<?php
///////////////////////////////////////////////////////////////////////////////////
//  Main CMS module
//
//  @package LizzardmanEngine 
//  @authors Artem Zelinskyi [lizzardman@], ...
//  @copyright 2012 
//  @version $Id$
//  @since 0.0.0
//  @license http://opensource.org/licenses/gpl-license.php GNU Public License
//  LizzardmanEngine is free software. This version may have been modified pursuant
//  to the GNU General Public License, and as distributed it includes or
//  is derivative of works licensed under the GNU General Public License or
//  other free or open source software licenses.
//  See COPYING.txt for copyright notices and details.
/////////////////////////////////////////////////////////////////////////////////////

    
// Main engine defines   
ini_set('display_errors', 'on');
error_reporting(E_ALL & ~E_NOTICE);

require_once "router.php";
require_once "view.php";
session_set_cookie_params(60 * 30, "/");
session_start();

//var_dump($_REQUEST);
Router::getInstance()->request($_REQUEST);


?>
