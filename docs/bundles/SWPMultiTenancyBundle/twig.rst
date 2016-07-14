Twig Extension
==============

This bundle provides Twig global variables in your project. See below for more details.

Global variables
----------------

- ``organization`` - provides data about the current organization/tenant. This variable is an object of type :ref:`component_tenant_model_tenant-interface`

Usage:

.. code-block:: twig

    {{ organization.name }}
    {{ organization.subdomain }}
    {# ... #}
