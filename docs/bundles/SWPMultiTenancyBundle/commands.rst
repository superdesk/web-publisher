Console Commands
----------------

This section describes console commands available in this bundle.

Create a new organization
~~~~~~~~~~~~~~~~~~~~~~~~~

To make use of this bundle, you need to first create the default organization.
You may also need to create some other, custom organization if needed.

.. note::

    This command persists organizations in database depending on your enabled persistence backend.
    If the PHPCR backend is enabled it will store tenants in PHPCR tree.

To create the default organization, execute the following console command:

.. code-block:: bash

    php app/console swp:organization:create --default

To create a custom organization which will be disabled by default, use the command:

.. code-block:: bash

    php app/console swp:organization:create --disabled

To create a custom organization, execute the following console command:

.. code-block:: bash

    php app/console swp:organization:create

Run ``php app/console swp:organization:create --help`` to see more details of how to use this command.

List available organizations
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This command list all available organizations.

Usage:

.. code-block:: bash

    php app/console swp:organization:list

Run ``php app/console swp:organization:list --help`` to see more details of how to use this command.

Create a new tenant
~~~~~~~~~~~~~~~~~~~

To make use of this bundle, you need to first create the default tenant.
You may also need to create some other, custom tenants.

.. note::

    This command persists tenants in database depending on your enabled persistence backend.
    If the PHPCR backend is enabled it will store tenants in PHPCR tree.

To create the default tenant, execute the following console command:

.. code-block:: bash

    php app/console swp:tenant:create --default

.. note::

    When creating default tenant the command requires you to have the default organization created.

To create a custom tenant which will be disabled by default, use the command:

.. code-block:: bash

    php app/console swp:tenant:create --disabled

To create a custom tenant, execute the following console command:

.. code-block:: bash

    php app/console swp:tenant:create

You will need to specify organization unique code so tenant can be assigned to the organization.

Run ``php app/console swp:tenant:create --help`` to see more details of how to use this command.
