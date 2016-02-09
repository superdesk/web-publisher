Models
======

.. _component_tenant_model_tenant:

Tenant
------

Every tenant is represented by a **Tenant** model which by default has the following properties:

+--------------+-------------------------------------------+
| Method       | Description                               |
+==============+===========================================+
| id           | Unique identifier                         |
+--------------+-------------------------------------------+
| subdomain    | Tenant's subdomain                        |
+--------------+-------------------------------------------+
| name         | Tenant's name                             |
+--------------+-------------------------------------------+
| enabled      | Indicates whether the tenant is enabled   |
+--------------+-------------------------------------------+
| createdAt    | Date of creation                          |
+--------------+-------------------------------------------+
| updatedAt    | Date of last update                       |
+--------------+-------------------------------------------+
| deletedAt    | Indicates whether the tenant is deleted   |
+--------------+-------------------------------------------+

.. note::

    This model implements :ref:`component_tenant_model_tenant-interface`.
