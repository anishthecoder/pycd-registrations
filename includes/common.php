<?php
/**
 * Defines the base path to the root of the web folder, accessible from any
 * included function or file.
 */
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/competitions');
define('CONTROLLER', '"controller.php"');

// Includes --------------------------------------------------------------------
/* Helper Classes */
require_once BASE_PATH.'/includes/functions.php';
require_once BASE_PATH.'/includes/helpers/Config.php';

/* Others */
require_once BASE_PATH.'/includes/helpers/Db.php';

/* Libraries */
require_once BASE_PATH.'/lib/Phamlp/sass/SassParser.php';

/* Template */
require_once BASE_PATH.'/includes/helpers/Template.php';

/* Models */
require_once BASE_PATH.'/includes/models/User.php';
require_once BASE_PATH.'/includes/models/SportsEvent.php';
require_once BASE_PATH.'/includes/models/Church.php';
require_once BASE_PATH.'/includes/models/Player.php';

/* Views */
require_once BASE_PATH.'/includes/helpers/Render.php';

/* Controllers */
require_once BASE_PATH.'/includes/controllers/Controller.php';
require_once BASE_PATH.'/includes/controllers/Session.php';
require_once BASE_PATH.'/includes/controllers/SportsController.php';
require_once BASE_PATH.'/includes/controllers/AdminController.php';


// Other global settings -------------------------------------------------------
date_default_timezone_set('America/Chicago');

// Automatically start the session because everything in the app is
// sesssion-based.
session_start();
