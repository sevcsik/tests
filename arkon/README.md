# PHP Test Excercise
## PHP Backend Developer position at [Arkon Zrt.] (http://arkon.hu)

This sample project is a system for tracking students, schools, and
educations, implemented with the wonderful [Symfony2] (http://symfony.com)
framework.

## Getting Started ##

By default, this distribution comes with a sample database in the file
_doc/sample.sql_ (MySQL), and pictures in the web/upload folder.

For the maximum user experience, web/upload and app/cache has to be writable
by the web server.

The database connection must be configured in _app/config/parameters.ini_.
If you choose not to import the sample sql, you can create the database
with the

  $ php app/console doctrine:database:create
  $ php app/console doctrine:schema:update --force

commands.

If any problem arises, check _web/config.php_ to make sure that every
prerequisite is met for running Symfony.

## Management ##

The main script is located at web/app\_dev.php.

The Symfony developer tools are enabled, and two hardcoded logins are 
available, admin:admin and user:user. This allows can create users from the 
admin UI, without the need to manually insert one in the database.

Later, you can remove these users by removing the entries under the
in\_memory section in app/config/security\_dev.php.

You can configure your web server's url rewrite module to pass paths to
app\_dev.php to use friendly urls.

## Documentation ##

There's a PHPDocumentor2 generated HTML documentation in _doc/_. You can
also find an entity-relationship diagram in _doc/entities.pdf_.

Have fun!
