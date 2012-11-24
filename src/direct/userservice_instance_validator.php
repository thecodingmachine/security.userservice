<?php
use Mouf\MoufUtils;

use Mouf\MoufManager;

// This file validates that a "splash" instance exists.
// If not, an alert is raised.

require_once '../../../../../mouf/Mouf.php';

// Note: checking rights is done after loading the required files because we need to open the session
// and only after can we check if it was not loaded before loading it ourselves...
MoufUtils::checkRights();

if (isset($_REQUEST['selfedit'])) {
	$selfedit = $_REQUEST['selfedit'];
} else {
	$selfedit = "false";
}

$jsonObj = array();

$instanceExists = MoufManager::getMoufManager()->instanceExists('userService');

if ($instanceExists) {
	$jsonObj['code'] = "ok";
	$jsonObj['html'] = "'userService' instance found";
} else {
	$jsonObj['code'] = "warn";
	$jsonObj['html'] = "Unable to find the 'userService' instance. 
If you plan to use the UserService package, it is usually a good idea to create an instance of the UserService class (or a subclass) named 'userService'. Click here to <a href='".MOUF_URL."mouf/newInstance2?instanceName=userService&instanceClass=UserService&selfedit=".$selfedit."'>create an instance of the UserService class named 'userService'</a>.";
}

echo json_encode($jsonObj);
exit;

?>