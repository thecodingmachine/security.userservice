<?php
namespace Mouf\Security\UserService;

/**
 * This interface can be implemented by any class that wants to track when a user logs in or logs out.
 * The class must be registered in the UserService instance to be called.
 *
 */
interface AuthenticationListenerInterface {
	
	/**
	 * This method is called just after a log-in occurs.
	 *
	 * @param UserServiceInterface $userService The service that performed the log-in
	 */
	public function afterLogIn(UserServiceInterface $userService);
	
	/**
	 * This method is called just before the current user logs out.
	 *
	 * @param UserServiceInterface $userService The service that performed the log-out
	 */
	public function beforeLogOut(UserServiceInterface $userService);
}
?>