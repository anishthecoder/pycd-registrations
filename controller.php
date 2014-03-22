<?php
require_once('includes/common.php');

// Ensure that a controller was specified.
if (isset($_REQUEST[Controller::$ID])){
	$controller = $_REQUEST[Controller::$ID];
}
else {
	return;
}

// Ensure that an action was specified.
if (isset($_REQUEST[Controller::$ACTION])){
	$action = $_REQUEST[Controller::$ACTION];
}
else {
	return;
}


// If the controller is anything OTHER than the Session controller, check for
// session validity.
if ($controller !== Session::$ID){
		if (!Session::isSessionActive() == TRUE){
			Render::jsRedirect('login.php');
		}
}

// Finally process the request.
$controller::$action($_REQUEST);

