<?php
/*
 * Copyright (c) 2012 David Negrier
*
* See the file LICENSE.txt for copying permission.
*/

MoufAdmin::getValidatorService()->registerBasicValidator('UserService validator', 'plugins/security/userservice/1.0/direct/userservice_instance_validator.php', array("selfedit"));
?>