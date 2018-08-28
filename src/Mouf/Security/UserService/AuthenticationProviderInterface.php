<?php
namespace Mouf\Security\UserService;

/**
 * Services implementing this interface can be used to check user's authentication and retrieve it.
 *
 */
interface AuthenticationProviderInterface
{
    
    /**
     * Returns "true" if the user is logged, "false" otherwise.
     *
     * @return boolean
     */
    public function isLogged(UserServiceInterface $userService): bool;

    
    /**
     * Returns the current user ID.
     *
     * @return string|int|null
     */
    public function getUserId(UserServiceInterface $userService);
    
    /**
     * Returns the current user login.
     *
     * @return string|null
     */
    public function getUserLogin(UserServiceInterface $userService): ?string;
    
    /**
     * Returns the user that is logged (or null if no user is logged).
     *
     * return UserInterface|null
     */
    public function getLoggedUser(UserServiceInterface $userService): ?UserInterface;
}
