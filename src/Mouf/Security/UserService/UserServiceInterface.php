<?php
namespace Mouf\Security\UserService;

/**
 * Services implementing this interface can be used to log in or log out users.
 *
 */
interface UserServiceInterface
{
    
    /**
     * Logs the user using the provided login and password.
     * Returns true on success, false if the user or password is incorrect.
     *
     * @param string $user
     * @param string $password
     * @return boolean.
     */
    public function login($user, $password);
    
    /**
     * Logs the user using the provided login.
     * The password is not needed if you use this function.
     * Of course, you should use this functions sparingly.
     * For instance, it can be useful if you want an administrator to "become" another
     * user without requiring the administrator to provide the password.
     *
     * @param string $login
     */
    public function loginWithoutPassword($login);
    
    /**
     * Logs a user using a token. The token should be discarded as soon as it
     * was used.
     *
     * @param string $token
     */
    public function loginViaToken($token);
    
    /**
     * Returns "true" if the user is logged, "false" otherwise.
     *
     * @return boolean
     */
    public function isLogged();

    /**
     * Redirects the user to the login page if he is not logged.
     *
     * @return boolean
     */
    public function redirectNotLogged();
    
    /**
     * Logs the user off.
     *
     */
    public function logoff();
    
    /**
     * Returns the current user ID.
     *
     * @return string
     */
    public function getUserId();
    
    /**
     * Returns the current user login.
     *
     * @return string
     */
    public function getUserLogin();
    
    /**
     * Returns the user that is logged (or null if no user is logged).
     *
     * return UserInterface
     */
    public function getLoggedUser();
}
