Console Commands
----------------

This section describes console commands available in this bundle.

Create a new tenant
~~~~~~~~~~~~~~~~~~~

To make use of this bundle, you need to first create the default tenant.
You may also need to create some other, custom tenants.

.. note::

    This command by default uses Doctrine ORM to persist tenants into the database.

To create the default tenant, execute the following console command:

.. code-block:: bash

    php app/console swp:tenant:create --default

To create a custom tenant which will be disabled by default, use the command:

.. code-block:: bash

    php app/console swp:tenant:create --disabled

Run ``php app/console swp:tenant:create --help`` to see more details of how to use this command.
