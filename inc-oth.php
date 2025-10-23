<?php
// Include file for non-web scripts (like cron jobs)
// This file provides database connectivity without the full web session setup

mb_http_output("UTF-8");
ob_start("mb_output_handler");
require_once("config.php");

date_default_timezone_set(CONST_TIME_ZONE);

// Set error reporting
switch(ERRORREPORTING){
    case "1": error_reporting(E_ALL); break;
    case "2": error_reporting(E_ERROR | E_PARSE); break;
    default: error_reporting(0);
}

require_once(CONST_INCLUDES_DIR."/ebiz-autoload.php");
require_once(CONST_INCLUDES_DIR . "general-func.php");

// Connect to database
\eBizIndia\PDOConn::connectToDB('mysql');

// Define constants that might be needed
if(!defined('CONST_DATE_DISPLAY_FORMAT')){
    define('CONST_DATE_DISPLAY_FORMAT', 'd-m-Y');
}
?>