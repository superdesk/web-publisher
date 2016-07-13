.. _bundle_tenant_routing-integration:

CMF RoutingBundle Integration
=============================

The SWPMultiTenancyBundle can be integrated with the `CMF RoutingBundle`_.
This section describes how to integrate the CMF Routing Bundle
when using PHPCR ODM or ORM as persistence backends.

.. note::

  If you don't have CMF RoutingBundle installed, `see the documentation`_
  on how to install and configure it.

Doctrine PHPCR ODM integration
------------------------------

Enable PHPCR persistence backend
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Make sure the PHPCR persistence backend is enabled in CMF RoutingBundle.

You need to enable the PHPCR as a persistence backend for the SWPMultiTenancyBundle and fully integrate this bundle
with the CMF RoutingBundle. Add the following lines to the configuration file:

.. code-block:: yaml

        # app/config/config.yml
        swp_multi_tenancy:
            persistence:
                phpcr:
                    # if true, PHPCR is enabled in the service container
                    enabled: true
                    # route base paths under which routes will be stored
                    route_basepaths: ["routes"]
                    # PHPCR content base path under which content will be stored
                    content_basepath: "content"

Once the ``enabled`` property is set to true, :ref:`bundle_tenant_initializer_tenant-initializer`,
:ref:`bundle_tenant_router_tenant-router` and :ref:`bundle_tenant_prefix_tenant-prefix`
will be available in the application.


Enable TenantAwareRouter
~~~~~~~~~~~~~~~~~~~~~~~~

To register the TenantAwareRouter service in the CMF RoutingBundle, add the following lines to your configuration file:

.. code-block:: yaml

    cmf_routing:
        chain:
            routers_by_id:
                # other routers
                # TenantAwareRouter with the priority of 150
                swp_multi_tenancy.tenant_aware_router: 150

The RoutingBundle example configuration can be found here:

.. code-block:: yaml

    cmf_routing:
        chain:
            routers_by_id:
                # default Symfony Router
                router.default: 200
                # TenantAwareRouter
                swp_multi_tenancy.tenant_aware_router: 150
                # CMF Dynamic Router
                cmf_routing.dynamic_router: 100
        dynamic:
            route_collection_limit: 100
            persistence:
                phpcr:
                    enabled: true

.. note::

  Please `see the documentation`_ of the CMF RoutingBundle for more details.


Doctrine ORM integration
------------------------

Not implemented yet.


.. _see the documentation: http://symfony.com/doc/master/cmf/bundles/routing/introduction.html
.. _CMF RoutingBundle: https://github.com/symfony-cmf/RoutingBundle
