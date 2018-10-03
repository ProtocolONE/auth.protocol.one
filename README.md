ProtocolOne Authentication
==========================

This service is part of the ProtocolOne ecosystem and serves to ensure the registration 
and authorization of users.

Prerequisites
-------------
- [PHP](https://secure.php.net/) 7.0 or above
- [MongoDB](https://www.mongodb.com/) 4.0 or above
- [OpenSSL](https://www.openssl.org/)
- [Composer](https://getcomposer.org/)

Installation
-------------
Install to the PHP of the extension [ext-mongodb](https://docs.mongodb.com/ecosystem/drivers/php/) 
and enable `ext-openssl` in your `php.ini` file.

You can use Doctrine MongoDB ODM with PHP 7, but there are a few extra steps during 
the installation. Since the legacy driver (referred to as `ext-mongo`) is not available 
on PHP 7, you will need the new driver (`ext-mongodb`) installed and use a polyfill to 
provide the API of the legacy driver. See instruction on the [introduction page](https://www.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/reference/introduction.html#using-php-7).

Install the dependencies by running the command:

    composer install
    
After that, generate the schema and documents in the database:

    php bin/console doctrine:mongodb:schema:create --index
    php bin/console doctrine:mongodb:generate:documents AppBundle
    
Configure available oAuth authentication. [More details](app/Resources/doc/oauth-client.md).

Documentation
-------------
- [Introduction](app/Resources/doc/introduction.md)
- [Registration by username and password](app/Resources/doc/registration-by-username.md)
- [Authentication by username and password](app/Resources/doc/authentication-by-username.md)
- [Password recovery](app/Resources/doc/password-recovery.md)
- [Registration and authentication over oAuth](app/Resources/doc/oauth-client.md)
- [Creating keys for JWT](app/Resources/doc/creating-keys-for-jwt.md)
- [Refresh access token](app/Resources/doc/refresh-access-token.md)

License
-------
The MIT License (MIT)

For the whole copyright, see the [LICENSE](LICENSE) file distributed with this 
source code.
