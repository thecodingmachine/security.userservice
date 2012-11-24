<?php
/*
 * Copyright (c) 2012 David Negrier
*
* See the file LICENSE.txt for copying permission.
*/

// FIXME: we should not call $_REQUEST. We need a static Context object that we can call instead and that
// contains the environment we are working on (config, DI container, etc...)
if (isset($_REQUEST['selfedit']) && $_REQUEST['selfedit']=="true") {
	$url = 'vendor/mouf/security.userservice/src/direct/userservice_instance_validator.php';
} else {
	$url = '../../../vendor/mouf/security.userservice/src/direct/userservice_instance_validator.php';
}
MoufAdmin::getValidatorService()->registerBasicValidator('UserService validator', $url, array("selfedit"));
?>