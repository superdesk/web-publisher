Configuring Publisher
=====================

Now that you have a working instance of Superdesk Publisher, you need to secure and configure it before deploying to 
a production environment. This guide will walk you through securing several components of Publisher.

The Configuration File
----------------------

Publisher's main configuration file is `/var/www/publisher/.env`. This file is tracked in source control and contains 
the default configuration values for Publisher. Edits SHOULD NOT be made directly to this file. Rather, create a new file 
called `.env.local` in `/var/www/publisher` and add your custom values there. To start, let's switch the Publisher 
environment from dev to prod and turn off debug mode. Edit `/var/www/publisher/.env.local` and add the following values:

.. code-block::

	APP_ENV=prod
	APP_DEBUG=0

You can view `/var/www/publisher/.env` for a full list of configurable values.

.. TIP::

	You can create environment-specific configuration by creating .env files with a naming scheme of `.env.$APP_ENV.local`.

Another important configuration option is your `SWP_DOMAIN`. Add it to your `.env.local`:

.. code-block::

	SWP_DOMAIN=example.com

Add an App Secret
-----------------

Your app secret is by default set to a static value and checked-in to source control. You must change your app secret to 
secure your Publisher instance. You can use a service like `Nux`_ to generate a random secret key. Once you have your 
secret key, add it to your `.env.local`:

.. code-block::

	APP_SECRET=pasteYourSecretKeyHere

Secure your Database
--------------------

If you followed the Publisher installation guide found in this documentation, your database is currently open to 
the internet without a password.

First, require all users use password authentication to log in to PostgreSQL. Using the same method you used in the 
installation guide, edit `pg_hba.conf` and change all `trust` values to `md5`.

Next, `add a password`_ to your root PostgreSQL account.

Once your root user has a password, update the connection string by adding to your `.env.local`:

.. code-block::

	DATABASE_URL=pgsql://root:yourPasswordHere@127.0.0.1/publisher_%kernel.environment%?charset=utf8&serverVersion=9

Reload the PostgreSQL `pg_hba.conf`:

.. code-block:: bash

	psql -t -P format=unaligned -c 'select pg_reload_conf()';

Finally, refresh the publisher cache:

.. code-block:: bash

	php bin/console cache:clear

For added security, you can create a new PostgreSQL user with limited permissions.

Configure Publisher Through Superdesk
-------------------------------------

Publisher integrates into Superdesk simply by adding new option *Publisher Settings* to its main left-hand sidebar navigation.

.. image:: publisher-configuration-01.png
   :alt: Publisher Settings option
   :align: center

This option, when chosen, opens Publisher configuration which allows configuring one or more websites. 
Setting a website actually means defining routes, creating navigation menus (whose menu items are linked to these routes), 
and creating content lists. 

Detailed explanation of website management steps can be found in chapter :doc:`Admin interface </manual/admin_interface/index>`

.. _Nux: http://nux.net/secret
.. _add a password: https://www.postgresql.org/docs/8.0/sql-alteruser.html