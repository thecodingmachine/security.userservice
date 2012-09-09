<?php
namespace Mouf\Security\UserService;

/**
 * DAOs implementing this interface can be used to query users from a database (or anywhere else).
 *
 */
interface UserDaoInterface {
	
	/**
	 * Returns a user from its login and its password, or null if the login or credentials are false.
	 *
	 * @param string $login
	 * @param string $password
	 * @return UserInterface
	 */
	public function getUserByCredentials($login, $password);

	/**
	 * Returns a user from its token.
	 *
	 * @param string $token
	 * @return UserInterface
	 */
	public function getUserByToken($token);
	
	/**
	 * Discards a token.
	 *
	 * @param string $token
	 */
	public function discardToken($token);
	
	/**
	 * Returns a user from its ID
	 *
	 * @param string $id
	 * @return UserInterface
	 */
	public function getUserById($id);
	
	/**
	 * Returns a user from its login
	 *
	 * @param string $login
	 * @return UserInterface
	 */
	public function getUserByLogin($login);
}
?>