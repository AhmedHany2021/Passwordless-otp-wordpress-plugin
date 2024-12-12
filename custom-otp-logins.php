<?php

/*
Plugin Name: Custom OTP Login
Plugin URI: https://github.com/AhmedHany2021
Description: This plugin enable the password less registration and login via otp
Version: 1.0
Author: Ahmed Hany
Author URI: https://github.com/AhmedHany2021
*/

namespace CUSTOMOTP;

if (!defined('ABSPATH'))
{
    die();
}

/* Add the main global variables */

if(!defined("COTP_BASEDIR")) { define("COTP_BASEDIR",__DIR__ . '/'); }
if(!defined("COTP_INC")) { define("COTP_INC",COTP_BASEDIR.'includes' . '/'); }
if(!defined("COTP_TEMPLATES")) { define("COTP_TEMPLATES",COTP_BASEDIR.'templates' . '/'); }
if(!defined("COTP_URI")) { define("COTP_URI",plugin_dir_url(__FILE__) . '/'); }
if(!defined("COTP_ASSETS")) { define("COTP_ASSETS", COTP_URI.'assets' . '/'); }

/* Add the autoload class */
require_once COTP_INC.'autoload.php';
use CUSTOMOTP\Includes\autoload;
autoload::fire();

/* register the form class */
use CUSTOMOTP\Includes\FormHandlerClass;
$formhandler = new FormHandlerClass();
