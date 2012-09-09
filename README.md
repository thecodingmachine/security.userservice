Managing users with Mouf
========================

This package is part of the Mouf PHP framework and contains objects and interfaces to manage users.

The Mouf framework is only an IOC framework. As such, it does not provide any means for managing users. Hopefully, the Mouf team provides this "userservice" package that can do help you with user management.

The "userservice" package
-------------------------

This package provides components you can use to implement user authentication. This is not an "out-of-the-box" package.
In order to use this package, you will have to develop some components on your side. This package will provide utility functions
to log a user, to know whether a user is logged or not, .... This package does not provide any way to store or retrieve users from your database. This is up to you.

The package contains these classes and interfaces:
- A <b>UserService</b> class: this is the main class. It can be used to login a user, to logout the user, to know whether a user is logged or not, etc...
- A <b>UserServiceInterface</b> interface: most libraries relying on the "userservice" will rely on this interface. If the default
  <b>UserService</b> class does not meet your requirements, you can develop you own "userservice" instance that will implement the <b>UserServiceInterface</b>
  interface.
- The <b>UserService</b> class will require a Data Access Object to access your database. The DAO is not part of this package,
  therefore, you will have to provide it. You DAO will need to extend the <b>UserDaoInterface</b> interface.
- Finally, objects returned by your <b>UserDao</b> class will implement the <b>UserInterface</b> interface.


The one thing you must remember when using the "userservice" package is this: You provide the "userservice" package with a DAO that will help 
it to access your database, and the userservice will help you manage your users login/logout in return.


The <b>UserService</b> component
------------------------------------

This component has 3 required properties that must be wired using the Mouf User Interface:

- <b>loginPageUrl</b>: This is the page containing the login form. When a user is not connected and should be, it will be redirected
  to this page.
- <b>userDao</b>: This is the Data Access Object that will query your database to know if the user credentials are valid.
- <b>log</b>: The logger used to log messages.

There is also one optional property:

- <b>authenticationListeners</b>: A list of components that will be notified when a user logs in or logs out.


The <b>UserService</b> contains these methods:

```php
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
```

You will use the <em>login</em> method to log a user. The method returns false if the credentials are invalid.
You will use the <em>logoff</em> method to log-off a user. The <em>isLogged</em> method will let you know if
a user is logged or not. By using the <em>getUserId</em> and <em>getUserLogin</em> functions, you will get
information about the current logged user. Finally the <em>redirectNotLogged</em> function will check whether
the current user is logged or not. If not, it will redirect the user to the login page.

For the UserService to work, you will need to provide a UserDao implementing this interface:

```php
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
```

Note: the token related method only needs to be implemented if you want to provide token based authentication. This
can be very useful for automated password recovery based on the mail of the user for instance.

Finally, the UserDao you will provide will return objects representing Users. These objects will need to implement
the UserInterface interface:

```php
/**
 * Objects implementing the UserInterface represent a user.
 *
 */
interface UserInterface {
	
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
```