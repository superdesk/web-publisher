StorageBundle
=============

This bundle provides tools to build a persistence-agnostic storage layer.

:doc:`/components/Storage/index`, which is used by this bundle, provides generic interfaces to create different types of repositories, factories, and drivers
which can be used for various storages.
So far, this bundle supports:

- Doctrine ORM
- Doctrine PHPCR ODM

By default this bundle uses the Doctrine ORM persistence backend. If you would like to make use of, for example,
Doctrine PHPCR, you would need to install and configure `DoctrinePHPCRBundle`_.

.. toctree::
    :numbered:
    :maxdepth: 2

    prerequisites
    installation
    usage

.. _DoctrinePHPCRBundle: https://github.com/doctrine/DoctrinePHPCRBundle
