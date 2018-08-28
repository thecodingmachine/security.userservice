<?php
namespace Mouf\Security\UserService;

/**
 * DAOs implementing this interface can be used to query users from a database (or anywhere else).
 *
 */
interface UserDaoInterface
{
    
    /**
     * Returns a user from its login and its password, or null if the login or credentials are false.
     *
     * @param string $login
     * @param string $password
     * @return UserInterface|null
     */
    public function getUserByCredentials(string $login, string $password): ?UserInterface;

    /**
     * Returns a user from its token.
     *
     * @param string $token
     * @return UserInterface|null
     */
    public function getUserByToken(string $token): ?UserInterface;
    
    /**
     * Discards a token.
     *
     * @param string $token
     */
    public function discardToken(string $token): void;
    
    /**
     * Returns a user from its ID
     *
     * @param string|int $id
     * @return UserInterface
     */
    public function getUserById($id): ?UserInterface;
    
    /**
     * Returns a user from its login
     *
     * @param string $login
     * @return UserInterface
     */
    public function getUserByLogin(string $login): ?UserInterface;
}
