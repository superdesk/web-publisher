Interfaces
==========

Model Interfaces
----------------

.. _component_tenant_model_tenant-interface:

TenantInterface
~~~~~~~~~~~~~~~

This interface should be implemented by every tenant.

.. note::

    This interface extends TimestampableInterface, EnableableInterface and SoftDeleteableInterface.


.. _component_tenant_model_tenant-aware-interface:

TenantAwareInterface
~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models associated
with a specific tenant.


Service Interfaces
------------------

.. _component_tenant_context_tenant-context-interface:

TenantContextInterface
~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by a service
responsible for managing the currently used :ref:`component_tenant_model_tenant`.

.. _component_tenant_provider_tenant-provider-interface:

TenantProviderInterface
~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by a service
responsible for providing all available tenants.

.. _component_tenant_resolver_tenant-resolver-interface:

TenantResolverInterface
~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by a service
responsible for resolving current :ref:`component_tenant_model_tenant`.


Repository Interfaces
---------------------

.. _component_tenant_repository_tenant-repository-interface:

TenantRepositoryInterface
~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by repositories responsible
for storing the :ref:`component_tenant_model_tenant` objects.

Factory Interfaces
------------------

.. _component_tenant_factory_factory-interface:

TenantFactoryInterface
~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by tenant factories which are
responsible for creating an objects of type :ref:`component_tenant_model_tenant-interface`.
