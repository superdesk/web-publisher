MultiTenancyBundle
==================

This bundle provides the tools to build multi-tenant architecture for your PHP applications.

:doc:`/components/MultiTenancy/index`, which is used by this bundle, provides a generic interfaces to create different type of providers
which can be used to provide tenants from various storages.
So far, we aim to support:

- Doctrine ORM
- Doctrine PHPCR ODM
- configuration file (not yet implemented)

By default, this bundle uses Doctrine ORM to retrieve tenant objects from the storage.

.. toctree::
    :numbered:
    :maxdepth: 2

    prerequisites
    installation
    services
    models
    interfaces
    commands
    twig
    configuration
    tutorials/index
