Userservice's value obect
=========================

The `UserService` comes with a utility class named `CurrentUserIdValue`.
This class implements the [`ValueInterface`](http://mouf-php.com/packages/mouf/utils.value.value-interface/README.md).

[Learn more about the `ValueInterface` here](http://mouf-php.com/packages/mouf/utils.value.value-interface/README.md)

The `CurrentUserIdValue` returns the ID of the current logged user when the  `val()` method is called.
It triggers an exception if no user is logged.

This class can be useful with many packages consuming the `ValueInterface`. For instance, you could use the `CurrentUserIdValue`
to inject the user id of the logged user into a SQL query rendered via an [Evolugrid](http://mouf-php.com/packages/mouf/html.widgets.evolugrid/README.md).