<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 29/04/15
 * Time: 15:39
 */

namespace Mouf\Security\UserService;

interface RememberMeProviderInterface
{

    /**
     * Identify the current user by the "remember me" cookie if exists
     */
    public function getUserByRememberMeToken(string $token): ?UserInterface;

    /**
     * Sets the "remember me" cookie value into the user
     * @param UserInterface $user
     * @param string $token
     */
    public function setUserRememberMeToken(UserInterface $user, string $token): void;
}
