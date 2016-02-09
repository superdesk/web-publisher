Console Commands
----------------

This section describes available in this bundle console commands.

Create a new tenant
~~~~~~~~~~~~~~~~~~~

To make use of this bundle, you need to first create the default tenant.
You may also need to create some other, custom tenants.
This command will do it for you! All you need to do is open the terminal
and execute the following console command:

.. note::

    This command by default uses Doctrine ORM to persist tenants into the database.

Usage:

.. code-block:: bash

    $ php app/console swp:tenant:create


To create the default tenant:

.. code-block:: bash

    $ php app/console swp:tenant:create --default


To create custom tenant which will be disabled by default:

.. code-block:: bash

    $ php app/console swp:tenant:create --disabled

Run ``php app/console swp:tenant:create --help`` to see more details on how to use this command.
