Configuration Reference
=======================

The SWPMultiTenancyBundle can be configured under the ``swp_multi_tenancy`` key in your configuration file.
This section describes the whole bundle's configuration.

.. _reference-configuration-tenant-configuration:

Configuration
-------------

.. code-block:: yaml

        # app/config/config.yml
        swp_multi_tenancy:
            resources:
                tenant:
                    classes:
                        model: SWP\Component\MultiTenancy\Model\Tenant
                        factory: SWP\Component\MultiTenancy\Factory\TenantFactory
            persistence:
                phpcr:
                    enabled: true
                    route_basepaths: ["routes"]
                    content_basepath: "content"
                    tenant_aware_router_class: SWP\MultiTenancyBundle\Routing\TenantAwareRouter
                    site_document_class: SWP\MultiTenancyBundle\Document\Site
                    document_class: SWP\MultiTenancyBundle\Document\Page


``resources``
.............

``tenant``
""""""""""

``classes``
"""""""""""

.. code-block:: yaml

        # app/config/config.yml
        swp_multi_tenancy:
            resources:
                tenant:
                    classes:
                        model: SWP\Component\MultiTenancy\Model\Tenant
                        factory: SWP\Component\MultiTenancy\Factory\TenantFactory

``model``
*********

**type**: ``string`` **default**: ``SWP\Component\MultiTenancy\Model\Tenant``

The FQCN of the Tenant model class which is of type :ref:`component_tenant_model_tenant-interface`.

``factory``
***********

**type**: ``string`` **default**: ``SWP\Component\MultiTenancy\Factory\TenantFactory``

The FQCN of the Tenant Factory class.

``persistence``
...............

``phpcr``
"""""""""

.. code-block:: yaml

        # app/config/config.yml
        swp_multi_tenancy:
            persistence:
                phpcr:
                    enabled: true
                    route_basepaths: ["routes"]
                    content_basepath: "content"
                    tenant_aware_router_class: SWP\MultiTenancyBundle\Routing\TenantAwareRouter
                    site_document_class: SWP\MultiTenancyBundle\Document\Site
                    document_class: SWP\MultiTenancyBundle\Document\Page

``enabled``
***********

**type**: ``boolean`` **default**: ``false``

If ``true``, PHPCR is enabled in the service container.

PHPCR can be enabled by multiple ways such as:

    .. code-block:: yaml

        phpcr: ~ # use default configuration
        # or
        phpcr: true # straight way
        # or
        phpcr:
            route_basepaths: ... # or any other option under 'phpcr'

``route_basepaths``
*******************

**type**: ``array`` **default**: ``['routes']``

A set of paths where routes should located in the PHPCR tree.

``content_basepath``
********************

**type**: ``string`` **default**: ``content``

The basepath for content objects in the PHPCR tree. This information is used
to offer the correct subtree to select content documents.

``site_document_class``
***********************

**type**: ``string`` **default**: ``SWP\MultiTenancyBundle\Document\Site``

Site document fully qualified class name to use. This document represents current site/tenant in PHPCR tree.

``tenant_aware_router_class``
*****************************

**type**: ``string`` **default**: ``SWP\MultiTenancyBundle\Routing\TenantAwareRouter``

TenantAwareRouter service's fully qualified class name to use.

``document_class``
******************

**type**: ``string`` **default**: ``SWP\MultiTenancyBundle\Document\Page``

The class for the pages used by ``PHPCRBasePathsInitializer``. You can provide your own class if using custom class.
