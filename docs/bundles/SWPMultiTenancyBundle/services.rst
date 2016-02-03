Services
========

TenantContext
-------------

The **TenantContext** service allows you to manage the currently used tenant.
Its ``getTenant`` method gets current tenant by resolving its subdomain from the request.

For example, if the host name is: ``subdomain.example.com`` the TenantContext will first
resolve the subdomain from the host matching agains provided in parameters file domain,
and then it will try to find the object of instance :ref:`component_tenant_context-tenant-interface` in the storage.
When found, it will return tenant object.

You can also set current tenant in the context, so whenever you request to get current tenant from the context
it will return you the object you set. ``setTenant`` method is used to set the tenant. It accepts as a first parameter
object of type :ref:`component_tenant_context-tenant-interface`.

.. _bundle_tenant_router_tenant-router:

TenantAwareRouter
-----------------

.. note::

  This service requires CMF Routing Bundle to be installed and configured.

TenantAwareRouter generates tenant aware routes. It extends `DynamicRouter`_ from the CMF Routing Bundle.

In some cases you may need to generate statically configured route.
Let's say we have path defined in PHPCR: ``/swp/default/routes/articles/features``.
For instance, if you want to generate route for current tenant in Twig template, use the following code:

.. code-block:: twig

    <a href="{{ path('/routes/articles/features') }}">Features</a>

The TenantAwareRouter will resolve current tenant from the host name and will internally create a route like
``/swp/default/routes/articles/features`` where ``swp`` is the root path defined in bundle config,
``default`` is the current tenant's subdomain, ``routes`` is the configured ``route_basepaths``.

The result will be:

.. code-block:: html

    <a href="/articles/features">Features</a>

You can also generate route by content path:

.. code-block:: twig

    <a href="{{ path(null, {'content_id': '/content/articles/features'}) }}">Features</a>


If the content is stored under the path ``/swp/default/content/articles/features`` in PHPCR tree, the router
will search for the route for that content and will return the route associated with it, in this case
the associated route is ``/swp/default/routes/articles/features`` so at the end
it will generate the same route: ``/articles/features`` as in the example above.

.. note::

    We do not recommend to hardcode the route name in the template because if the route will be removed,
    the page will break.

See :ref:`bundle_tenant_router-integration` on how to enable and make use of this router.

.. _bundle_tenant_prefix_tenant-prefix:

PrefixCandidates
----------------

.. note::

  This service requires CMF Routing Bundle to be installed and configured.

It extends Symfony CMF RoutingBundle PrefixCandidates service, to set tenant aware prefixes.
Prefixes are used to generate tenant aware routes. Prefixes are build from configured root path,
which by default is ``/swp`` and from ``route_basepaths`` which you can configure in the config file.

See the :ref:`reference-configuration-tenant-configuration` reference for more details.

PHPCR-ODM Repository Initializer
================================

.. _bundle_tenant_initializer_tenant-initializer:

PHPCRBasePathsInitializer
-------------------------

.. note::

  This service requires DoctrinePHPCRBundle to be installed and configured.

The Initializer is the PHPCR equivalent of the ORM schema tools.
PHPCRBasePathsInitializer creates base paths in content repository based on provided from storage
tenants, config and registers PHPCR node types. Disabled by default, can be enabled in config.

You can execute this initializer, together with the generic one, by running the following command:

.. code-block:: bash

  $ php app/console doctrine:phpcr:repository:init

Running the above command will trigger the generic initializer which is provided by the DoctrinePHPCRBundle.
The generic initializer will be fired before this one, and will create the root base path in the content
repository.

See :ref:`bundle_tenant_router-integration` on how to enable this initializer.


Repositories
============

TenantRepository
----------------

This repository allows you to fetch single tenant by its subdomain name and all available
tenants from the Doctrine ORM storage. It extends `EntityRepository`_ from Doctrine.

This service implements :ref:`component_tenant_repository_tenant-repository-interface` and it has two methods:

- findBySubdomain($subdomain) - Finds the tenant by subdomain. ``$subdomain`` is the subdomain of string type.
- findAvailableTenants() - Finds all available tenants. Returns an array of all tenants.


SQL Query Filters
=================

.. _bundle_tenant_filter_tenantable-filter:

TenantableFilter
----------------

This filter adds the where clause to the select queries, to make sure the query will be executed for current tenant.
if the tenant exists in the context and the tenant id is 1, it will add ``WHERE tenant_id = 1`` to every select query.
This way we always make sure we get the data for current tenant.

In order to make use of the filter every class needs to implement :ref:`component_tenant_model_tenant-aware-interface`
 which indicates that it should be associated with the specific tenant.

It extends ``Doctrine\ORM\Query\Filter\SQLFilter``.


Event Listeners
===============

TenantableListener
------------------

This event listener runs on every kernel request (``kernel.request`` event). If there is the tenant set in the
TenantContext it enables Doctrine ORM Query :ref:`bundle_tenant_filter_tenantable-filter`, else it doesn't do anything.
It's responsibility is to ensure that every SQL select query will be tenant aware (``tenant_id`` will be added
in the query).

TenantSubscriber
----------------

It subscribes to every Doctrine ORM ``prePersist`` event, when persisting the data.
It makes sure that persisted object (which needs to implement :ref:`component_tenant_model_tenant-aware-interface`)
will be associated with the current tenant when saving the object.

.. _EntityRepository: http://www.doctrine-project.org/api/orm/2.2/class-Doctrine.ORM.EntityRepository.html
.. _DynamicRouter: http://symfony.com/doc/current/cmf/bundles/routing/dynamic.html
