Configuration Reference
=======================

The SWPMultiTenancyBundle can be configured under the ``swp_multi_tenancy`` key in your configuration file.
This section describes the whole bundle's configuration.

.. _reference-configuration-tenant-configuration:

Full Default Configuration
--------------------------

.. code-block:: yaml

        # app/config/config.yml
        swp_multi_tenancy:
            use_orm_listeners: false
            persistence:
                phpcr:
                    enabled: true
                    basepath: "/swp"
                    route_basepaths: ["routes"]
                    content_basepath: "content"
                    menu_baseapth: "menu"
                    media_baseapth: "media"
                    tenant_aware_router_class: SWP\MultiTenancyBundle\Routing\TenantAwareRouter
                    classes:
                        tenant:
                            model: SWP\Component\MultiTenancy\Model\Tenant
                            repository: SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR\TenantRepository
                            factory: SWP\Component\MultiTenancy\Factory\TenantFactory
                            object_manager_name: ~
                        organization:
                            model: SWP\Component\MultiTenancy\Model\Organization
                            repository: SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR\OrganizationRepository
                            factory: SWP\Bundle\MultiTenancyBundle\Factory\OrganizationFactory
                            object_manager_name: ~
                orm:
                    enabled: true
                    classes:
                        tenant:
                            model: SWP\Component\MultiTenancy\Model\Tenant
                            repository: SWP\Bundle\MultiTenancyBundle\Doctrine\ORM\TenantRepository
                            factory: SWP\Component\MultiTenancy\Factory\TenantFactory
                            object_manager_name: ~
                        organization:
                            model: SWP\Component\MultiTenancy\Model\Organization
                            repository: SWP\Bundle\MultiTenancyBundle\Doctrine\ORM\OrganizationRepository
                            factory: SWP\Bundle\MultiTenancyBundle\Factory\OrganizationFactory
                            object_manager_name: ~


persistence
~~~~~~~~~~~

use_orm_listeners
.................

**type**: ``Boolean`` **default**: ``false``

Use this setting to activate the ``TenantableListener`` and ``TenantableSubscriber``. This will enable
tenantable SQL extension and will make sure your Doctrine ORM entities are tenant aware. See
:ref:`bundle_tenant_listeners_event-listeners` for more details.


``persistence``
...............

``phpcr``
"""""""""

    .. code-block:: yaml

        # app/config/config.yml
        swp_multi_tenancy:
            # ..
            persistence:
                phpcr:
                    enabled: true
                    basepath: "/swp"
                    route_basepaths: ["routes"]
                    content_basepath: "content"
                    menu_baseapth: "menu"
                    media_baseapth: "media"
                    tenant_aware_router_class: SWP\MultiTenancyBundle\Routing\TenantAwareRouter

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

``basepath``
************

**type**: ``string`` **default**: ``/swp``

The basepath for documents in the PHPCR tree.

``route_basepaths``
*******************

**type**: ``array`` **default**: ``['routes']``

A set of paths where routes should be located in the PHPCR tree.

``content_basepath``
********************

**type**: ``string`` **default**: ``content``

The basepath for content objects in the PHPCR tree. This information is used
to offer the correct subtree to select content documents.

``media_basepath``
******************

**type**: ``string`` **default**: ``media``

The basepath for media objects in the PHPCR tree. This information is used
to offer the correct subtree to select media documents.

``menu_basepath``
*****************

**type**: ``string`` **default**: ``media``

The basepath for menu objects in the PHPCR tree. This information is used
to offer the correct subtree to select menu documents.

``tenant_aware_router_class``
*****************************

**type**: ``string`` **default**: ``SWP\MultiTenancyBundle\Routing\TenantAwareRouter``

The TenantAwareRouter service's fully qualified class name to use.

``classes``
***********

.. code-block:: yaml

        # app/config/config.yml
        swp_multi_tenancy:
            # ..
            persistence:
                phpcr:
                    # ..
                    classes:
                        tenant:
                            model: SWP\Component\MultiTenancy\Model\Tenant
                            repository: SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR\TenantRepository
                            factory: SWP\Component\MultiTenancy\Factory\TenantFactory
                            object_manager_name: ~
                        organization:
                            model: SWP\Component\MultiTenancy\Model\Organization
                            repository: SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR\OrganizationRepository
                            factory: SWP\Bundle\MultiTenancyBundle\Factory\OrganizationFactory
                            object_manager_name: ~

``tenant.model``
****************

**type**: ``string`` **default**: ``SWP\Component\MultiTenancy\Model\Tenant``

The FQCN of the Tenant model class which is of type :ref:`component_tenant_model_tenant-interface`.

``tenant.factory``
******************

**type**: ``string`` **default**: ``SWP\Component\MultiTenancy\Factory\TenantFactory``

The FQCN of the Tenant Factory class.

``tenant.repository``
*********************

**type**: ``string`` **default**: ``SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR\TenantRepository``

The FQCN of the Tenant Repository class.

``tenant.object_manager_name``
******************************

**type**: ``string`` **default**: ``null``

The name of the object manager. If set to null it defaults to `default`.
If PHPCR ODM persistence backend is enabled it will register ``swp.object_manager.tenant`` service
which is an alias for "doctrine_phpcr.odm.default_document_manager".

``organization.model``
**********************

**type**: ``string`` **default**: ``SWP\Component\MultiTenancy\Model\Organization``

The FQCN of the Organization model class which is of type :ref:`component_tenant_model_organization-interface`.

``organization.factory``
************************

**type**: ``string`` **default**: ``SWP\Bundle\MultiTenancyBundle\Factory\OrganizationFactory``

The FQCN of the Organization Factory class.

``organization.repository``
***************************

**type**: ``string`` **default**: ``SWP\Bundle\MultiTenancyBundle\Doctrine\PHPCR\OrganizationRepository``

The FQCN of the Organization Repository class.

``organization.object_manager_name``
************************************

**type**: ``string`` **default**: ``null``

The name of the object manager. If set to null it defaults to `default`.
If PHPCR ODM persistence backend is enabled it will register ``swp.object_manager.organization`` service
which is an alias for ``doctrine_phpcr.odm.default_document_manager``.

``orm``
"""""""

    .. code-block:: yaml

        # app/config/config.yml
        swp_multi_tenancy:
            # ..
            persistence:
                orm:
                    enabled: true

``enabled``
***********

**type**: ``boolean`` **default**: ``false``

If ``true``, ORM is enabled in the service container.

ORM can be enabled by multiple ways such as:

    .. code-block:: yaml

        orm: ~ # use default configuration
        # or
        orm: true # straight way
        # or
        orm:
            enabled: true ... # or any other option under 'orm'

``classes``
***********

.. code-block:: yaml

        # app/config/config.yml
        swp_multi_tenancy:
            # ..
            persistence:
                orm:
                    # ..
                    classes:
                        tenant:
                            model: SWP\Component\MultiTenancy\Model\Tenant
                            repository: SWP\Bundle\MultiTenancyBundle\Doctrine\ORM\TenantRepository
                            factory: SWP\Component\MultiTenancy\Factory\TenantFactory
                            object_manager_name: ~
                        organization:
                            model: SWP\Component\MultiTenancy\Model\Organization
                            repository: SWP\Bundle\MultiTenancyBundle\Doctrine\ORM\OrganizationRepository
                            factory: SWP\Bundle\MultiTenancyBundle\Factory\OrganizationFactory
                            object_manager_name: ~

``tenant.model``
****************

**type**: ``string`` **default**: ``SWP\Component\MultiTenancy\Model\Tenant``

The FQCN of the Tenant model class which is of type :ref:`component_tenant_model_tenant-interface`.

``tenant.factory``
******************

**type**: ``string`` **default**: ``SWP\Component\MultiTenancy\Factory\TenantFactory``

The FQCN of the Tenant Factory class.

``tenant.repository``
*********************

**type**: ``string`` **default**: ``SWP\Bundle\MultiTenancyBundle\Doctrine\ORM\TenantRepository``

The FQCN of the Tenant Repository class.

``tenant.object_manager_name``
******************************

**type**: ``string`` **default**: ``null``

The name of the object manager. If set to null it defaults to `default`.
If Doctrine ORM persistence backend is enabled it will register ``swp.object_manager.tenant`` service
which is an alias for ``doctrine.orm.default_entity_manager``.

``organization.model``
**********************

**type**: ``string`` **default**: ``SWP\Component\MultiTenancy\Model\Organization``

The FQCN of the Organization model class which is of type :ref:`component_tenant_model_organization-interface`.

``organization.factory``
************************

**type**: ``string`` **default**: ``SWP\Bundle\MultiTenancyBundle\Factory\OrganizationFactory``

The FQCN of the Organization Factory class.

``organization.repository``
***************************

**type**: ``string`` **default**: ``SWP\Bundle\MultiTenancyBundle\Doctrine\ORM\OrganizationRepository``

The FQCN of the Organization Repository class.

``organization.object_manager_name``
************************************

**type**: ``string`` **default**: ``null``

The name of the object manager. If set to null it defaults to `default`.
If Doctrine ORM persistence backend is enabled it will register ``swp.object_manager.organization`` service
which is an alias for ``doctrine.orm.default_entity_manager``.
