MultiTenancyBundle
==================

This bundle provides the tools to build multi-tenant architecture for your PHP applications.

:doc:`/components/MultiTenancy/index`, which is used by this bundle, provides a generic interfaces to create different implementations of multi-tenancy in PHP applications.

The idea of this bundle is to have the ability to create multiple websites (tenants) within many organizations.

So far, we aim to support two persistence backends:

- Doctrine ORM
- Doctrine PHPCR ODM

.. note::

    This documentation describes installation and configuration for Doctrine PHPCR ODM at the moment.

**Features:**

- Allows to create many organizations
- Allows to create many websites (tenants) within a single organization
- Organization can have multiple websites assigned
- Each website have parent organization
- Allows to create default organization and default website (tenant)

E.g. The Vox Media organization can have multiple websites: The Verge. Polygon, Eater etc. Each website has it’s own content.

.. toctree::
    :numbered:
    :maxdepth: 2

    prerequisites
    installation
    services
    commands
    twig
    configuration
    tutorials/index
