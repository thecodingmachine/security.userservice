<?php
namespace Mouf\Security\UserService;

/**
 * Objects implementing the UserWithMailInterface represent a user that has a mail account.
 * These objects have more information on the user than objects implementing the classic UserInterface.
 *
 */
interface UserWithMailInterface extends UserInterface
{
    
    /**
     * Returns the full name of the user.
     *
     * @return string
     */
    public function getFullName();
    
    /**
     * Returns the email address of the current user.
     *
     * @return string
     */
    public function getEmail();
}
