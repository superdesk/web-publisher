Twig Extension
==============

This bundle provides Twig global variables in your project. See below for more details.

Global variables
----------------

- ``tenant`` - provides data about the current website/tenant. This variable is an object of type :ref:`component_tenant_model_tenant-interface`

Usage:

.. code-block:: twig

    {{ tenant.name }}
    {{ tenant.subdomain }}
    {{ tenant.organization.name }} # get tenant's organization name
    {# ... #}
