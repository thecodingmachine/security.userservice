<?php
/*
 * Copyright (c) 2012 David Negrier
*
* See the file LICENSE.txt for copying permission.
*/

// TODO: COMPLETELY DELETE THIS FILE! (before commit!)

if (isset($_REQUEST['selfedit']) && $_REQUEST['selfedit']=="true") {
    $url = 'vendor/mouf/security.userservice/src/direct/userservice_instance_validator.php';
} else {
    $url = '../../../vendor/mouf/security.userservice/src/direct/userservice_instance_validator.php';
}
MoufAdmin::getValidatorService()->registerBasicValidator('UserService validator', $url, array("selfedit"));
