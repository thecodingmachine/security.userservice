<?php
namespace Mouf\Security\UserService;

/**
 * Services implementing this interface can be used to check user's authentication and retrieve it.
 *
 */
interface AuthenticationProviderInterface {
	
	/**
	 * Returns "true" if the user is logged, "false" otherwise.
	 *
	 * @return boolean
	 */
	public function isLogged(UserServiceInterface $userService);

	
	/**
	 * Returns the current user ID.
	 *
	 * @return string
	 */
	public function getUserId(UserServiceInterface $userService);
	
	/**
	 * Returns the current user login.
	 *
	 * @return string
	 */
	public function getUserLogin(UserServiceInterface $userService);
	
	/**
	 * Returns the user that is logged (or null if no user is logged).
	 *
	 * return UserInterface
	 */
	public function getLoggedUser(UserServiceInterface $userService);
	
}
