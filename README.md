# AccessLimiterBundle
A symfony bundle to highly limit the access to a website, on a very restricted beta for instance.
An incoming visitor will see a simple page with a password field. If a correct password is entered, the website will then be accessible for this visitor.

# Configuration

~~~~
composer require ruano_a/access-limiter-bundle
~~~~

Add the bundle in Kernel.php, update the database.

Then create a access_limiter.yaml file in the config/packages directory.

The content of this file must be:

~~~~
access_limiter:
    passwords: ['password1', 'password2'] #mandatory, put the passwords that you want
    active: true #optional, true by default
    template_path: '@AccessLimiter/gate.html.twig' #optional, with our view by default
    listener_priority: 0 #optional, 0 by default.
~~~~

# Requirement 
Has been developed on Symfony 4.2.9, not tested on other version. At least the autowire is necessary.

# Notes
The form is protected against brute force attack: 3 wrongs tries -> blocked for 10mn (not configuration for now).
