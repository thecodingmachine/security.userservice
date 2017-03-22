<?php
namespace Mouf\Security\UserService;

/**
 * Objects implementing the UserInterface represent a user.
 *
 */
interface UserInterface
{
    
    /**
     * Returns the ID for the current user.
     *
     * @return string
     */
    public function getId();
    
    /**
     * Returns the login for the current user.
     *
     * @return string
     */
    public function getLogin();
}
