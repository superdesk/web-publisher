Twig Extension
==============

This bundle provides Twig global variables into your project. See below for more details.

Global variables
----------------

- ``organization`` - provides data about current organization/tenant. This variable is an object of type :ref:`component_tenant_model_tenant-interface`

Usage:

.. code-block:: twig

    {{ organization.name }}
    {{ organization.subdomain }}
    {# ... #}
