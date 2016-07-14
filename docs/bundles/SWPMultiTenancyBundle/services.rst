Services
========

TenantContext
-------------

The **TenantContext** service allows you to manage the currently used tenant.
Its ``getTenant`` method gets the current tenant by resolving its subdomain from the request.

For example, if the host name is: ``subdomain.example.com`` the TenantContext will first
resolve the subdomain from the host provided in the parameters file domain,
and then it will try to find the object of instance :ref:`component_tenant_context_tenant-context-interface` in the storage.
When found, it will return the tenant object.

You can also set the current tenant in the context, so whenever you request the current tenant from the context
it will return you the object you set. The ``setTenant`` method is used to set the tenant. It accepts as the first parameter an
object of type :ref:`component_tenant_context_tenant-context-interface`.

.. _bundle_tenant_router_tenant-router:

TenantAwareRouter
-----------------

.. note::

  This service requires the CMF Routing Bundle to be installed and configured.

The TenantAwareRouter generates tenant-aware routes. It extends `DynamicRouter`_ from the CMF Routing Bundle.

In some cases you may need to generate a statically configured route.
Let's say we have a path defined in PHPCR: ``/swp/default/routes/articles/features``.
If you want to generate a route for the current tenant in a Twig template, you could use the following code:

.. code-block:: twig

    <a href="{{ path('/routes/articles/features') }}">Features</a>

The TenantAwareRouter will resolve the current tenant from the host name and will internally create a route
``/swp/default/routes/articles/features`` where ``swp`` is the root path defined in the bundle configuration,
``default`` is the current tenant's subdomain, and ``routes`` is the configured ``route_basepaths``.

The result will be:

.. code-block:: html

    <a href="/articles/features">Features</a>

You can also generate the route by content path:

.. code-block:: twig

    <a href="{{ path(null, {'content_id': '/content/articles/features'}) }}">Features</a>

If the content is stored under the path ``/swp/default/content/articles/features`` in the PHPCR tree, the router
will search for the route for that content and will return the route associated with it. In this case,
the associated route is ``/swp/default/routes/articles/features`` so it will generate the same route:
``/articles/features`` as in the example above.

.. note::

    We do not recommend hard-coding the route name in the template because if the route is removed,
    the page will break.

See :ref:`bundle_tenant_routing-integration` on how to enable and make use of this router.

.. _bundle_tenant_prefix_tenant-prefix:

PrefixCandidates
----------------

.. note::

  This service requires the CMF Routing Bundle to be installed and configured.

This service extends Symfony CMF RoutingBundle PrefixCandidates service, to set tenant-aware prefixes.
Prefixes are used to generate tenant-aware routes. Prefixes are built from the configured root path,
which by default is ``/swp`` and from ``route_basepaths`` which you can set in the configuration file.

See the :ref:`reference-configuration-tenant-configuration` reference for more details.

PHPCR ODM Repository Initializer
================================

.. _bundle_tenant_initializer_tenant-initializer:

PHPCRBasePathsInitializer
-------------------------

.. note::

  This service requires DoctrinePHPCRBundle to be installed and configured.

The Initializer is the PHPCR equivalent of the ORM schema tools.
PHPCRBasePathsInitializer creates base paths in the content repository based on tenants, configures and registers PHPCR node types. It is disabled by default, but can be enabled in the configuration.

You can execute this initializer, together with the generic one, by running the following command:

.. code-block:: bash

  php app/console doctrine:phpcr:repository:init

Running this command will trigger the generic initializer which is provided by the DoctrinePHPCRBundle.
The generic initializer will be fired before this one, and will create the root base path in the content
repository.

See :ref:`bundle_tenant_routing-integration` on how to enable this initializer.


Repositories
============

TenantRepository
----------------

This repository allows you to fetch a single tenant by its subdomain name and all available
tenants from the Doctrine ORM storage. It extends `EntityRepository`_ from Doctrine.

This service implements :ref:`component_tenant_repository_tenant-repository-interface` and it has two methods:

- findBySubdomain($subdomain) - Finds the tenant by subdomain. ``$subdomain`` is the subdomain of string type.
- findAvailableTenants() - Finds all available tenants. Returns an array of all tenants.


SQL Query Filters
=================

.. _bundle_tenant_filter_tenantable-filter:

TenantableFilter
----------------

This filter adds the where clause to the select queries, to make sure the query will be executed for the current tenant.
If the tenant exists in the context and the tenant id is 1, it will add ``WHERE tenant_id = 1`` to every select query.
This way, we always make sure we get the data for the current tenant.

In order to make use of the filter every class needs to implement :ref:`component_tenant_model_tenant-aware-interface`
which indicates that it should be associated with the specific tenant.

It extends ``Doctrine\ORM\Query\Filter\SQLFilter``.


Event Listeners
===============

TenantableListener
------------------

This event listener runs on every kernel request (``kernel.request`` event). If the tenant is set in the
TenantContext it enables Doctrine ORM Query :ref:`bundle_tenant_filter_tenantable-filter`, otherwise it doesn't do anything.
Its responsibility is to ensure that every SQL select query will be tenant-aware (``tenant_id`` will be added
in the query).

TenantSubscriber
----------------

This subscribes to every Doctrine ORM ``prePersist`` event, when persisting the data.
It makes sure that the persisted object (which needs to implement :ref:`component_tenant_model_tenant-aware-interface`)
will be associated with the current tenant when saving the object.

.. _EntityRepository: http://www.doctrine-project.org/api/orm/2.2/class-Doctrine.ORM.EntityRepository.html
.. _DynamicRouter: http://symfony.com/doc/master/cmf/bundles/routing/dynamic.html
