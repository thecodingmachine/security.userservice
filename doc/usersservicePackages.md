The "userservice" package
========================

This package provides components you can use to implement user authentication. This is not an "out-of-the-box" package.
In order to use this package, you will have to develop some components on your side. This package will provide utility functions
to log a user, to know whether a user is logged or not, .... This package does not provide any way to store or retrieve users from your database. This is up to you.

The package contains these classes and interfaces:

- A <b>UserService</b> class: this is the main class. It can be used to login a user, to logout the user, to know whether a user is logged or not, etc...
- A <b>UserServiceInterface</b> interface: most libraries relying on the "userservice" will rely on this interface. If the default
  <b>UserService</b> class does not meet your requirements, you can develop your own "userservice" instance that will implement the <b>UserServiceInterface</b>
  interface.
- The <b>UserService</b> class will require a Data Access Object to access your database. The DAO is not part of this package,
  therefore, you will have to provide it. Your DAO will need to extend the <b>UserDaoInterface</b> interface.
- Finally, objects returned by your <b>UserDao</b> class will implement the <b>UserInterface</b> interface.


The one thing you must remember when using the "userservice" package is this: You provide the "userservice" package with a DAO that will help 
it to access your database, and the userservice will help you manage your users login/logout in return.

<br />
> Installation of UserService : [installation](usersserviceInstallation.md)