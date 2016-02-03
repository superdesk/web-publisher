Usage
=====

.. _component_tenant_context_tenant-context:

TenantContext
-------------

The **TenantContext** allows you to manage the currently used tenant.

.. code-block:: php

   <?php

   // ..
   use SWP\Component\MultiTenancy\Context\TenantContext;
   use SWP\Component\MultiTenancy\Model\Tenant;

   $tenant = new Tenant();
   $tenantContext = new TenantContext();

   $tenantContext->setTenant($tenant);

   var_dump($tenantContext->getTenant());

.. note::

   This service implements :ref:`component_tenant_context_tenant-context-interface`.

TenantProvider
--------------

The **TenantProvider** allows you to get all available tenants.

.. code-block:: php

   <?php

    // ..
   use SWP\Component\MultiTenancy\Provider\TenantProvider;

   $tenantProvider = new TenantProvider(/* SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface repository */);

   var_dump($tenantProvider->getAvailableTenants());

The ``getAvailableTenants`` method retrieves all tenants which ``enabled``
property is set to true and have been inserted in the given repository.

.. note::

   This service implements the :ref:`component_tenant_provider_tenant-provider-interface`.

TenantResolver
--------------

The **TenantResolver** allows you to resolve current tenant from the request.

.. code-block:: php

   <?php

   // ..
   use SWP\Component\MultiTenancy\Resolver\TenantResolver;

   $tenantResolver = new TenantResolver('example.com', /* SWP\Component\MultiTenancy\Repository\TenantRepositoryInterface repository */);

   var_dump($tenantResolver->resolve('tenant.example.com')); // will return an instance of TenantInterface.

The ``resolve`` method resolves the tenant based on current sudomain name. For example when the host is ``tenant.example.com``,
it will resolve the subdomain (``tenant``) and then it will search for the tenant in the given repository, by the resolved subdomain name. If the subdomain ``tenant`` is not found, it always returns the default tenant.

.. note::

   This service implements the :ref:`component_tenant_resolver_tenant-resolver-interface`.


TenantAwarePathBuilder
----------------------

The **TenantAwarePathBuilder** responsibility is to build PHPCR base paths which are tenant aware. This can build
whatever path is needed by the provided paths' names and given context.

.. code-block:: php

   <?php

   // ..
   use SWP\Component\MultiTenancy\PathBuilder\TenantAwarePathBuilder;
   use SWP\Component\MultiTenancy\Context\TenantContext;
   use SWP\Component\MultiTenancy\Model\Tenant;

   $tenant = new Tenant();
   $tenant->setSubdomain('example');
   $tenant->setName('Example tenant');
   $tenantContext = new TenantContext();
   $tenantContext->setTenant($tenant);

   $pathBuilder = new TenantAwarePathBuilder($tenantContext, '/swp');

   var_dump($pathBuilder->build('routes')); // will return: /swp/example/routes.
   var_dump($pathBuilder->build(['routes', 'content'])); // will return an array: ['/swp/example/routes', '/swp/example/routes']
   var_dump($pathBuilder->build('/')); // will return: /swp/default

The ``build`` method method builds the PHPCR path. It accepts as a first argument, string or an array of routes' names. The second argument is the context for the given path(s) name(s). In order to build the base paths, the TenantAwarePathBuilder's construct requires an object of type :ref:`component_tenant_resolver_tenant-resolver-interface` needs to be provided as a first argument, the object of type :ref:`component_tenant_context_tenant-context-interface` as a second argument and the root base path as a third argument.

.. note::

   This service implements the :ref:`component_tenant_resolver_tenant-resolver-interface`.
