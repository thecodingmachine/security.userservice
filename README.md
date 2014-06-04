UserService: Managing users with Mouf
=====================================

This package is part of the [Mouf PHP framework](http://mouf-php.com) and contains objects and interfaces to manage users' authentication.

It allows you to manage logged users. UserService does authentication, not authorization. If you want to manage
users rights, have a look at the [Right Service](http://mouf-php.com/packages/mouf/security.rightsservice/README.md).

Simply put, UserService:

- manages the user's session for you
- allows you to login / logout (`login()` and `logoff()` methods)
- knowns if the current user is logged or not (`isLogged()`)
- allows you to retrieve the current logged user (`getLoggedUser()`)

<br />
> Install UserService: [Installation](doc/usersserviceInstallation.md)