Creating a custom Tenant class
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In this tutorial you will learn how to create custom Tenant class. For example, you want
 to store info about tenant's theme name and you want to make use of it in your project.

.. note::

    This tutorial covers creating a custom Tenant class for PHPCR ODM.

This new class must implement :ref:`component_tenant_model_tenant-interface` which is provided by :doc:`/components/MultiTenancy/index`,
or you can extend the default :ref:`component_tenant_model_tenant` class, which is also part of the MultiTenancy Component.

Create an interface first which will require to implement theme name behaviour.

.. code-block:: php

    <?php

    namespasce Acme\AppBundle\Document;

    use SWP\Component\MultiTenancy\Model\TenantInterface as BaseTenantInterface;

    interface ThemeAwareTenantInterface extends BaseTenantInterface
    {
        /**
         * @return string
         */
        public function getThemeName();

        /**
         * @param string $themeName
         */
        public function setThemeName($themeName);
    }

Let's create a new Tenant class now:

.. code-block:: php

    <?php

    namespasce Acme\AppBundle\Document;

    use Acme\AppBundle\Document\ThemeAwareTenantInterface;
    use SWP\Component\MultiTenancy\Model\Tenant as BaseTenant;

    class Tenant extends BaseTenant implements ThemeAwareTenantInterface
    {
        /**
         * @var string
         */
        protected $themeName;

        /**
         * {@inheritdoc}
         */
        public function getThemeName()
        {
            return $this->themeName;
        }

        /**
         * {@inheritdoc}
         */
        public function setThemeName($themeName)
        {
            $this->themeName = $themeName;
        }
    }

Create a mapping file for your newly created document:

.. code-block:: yaml

    # src/Acme/AppBundle/Resources/config/doctrine/Document.Tenant.phpcr.yml

    Acme\AppBundle\Document\Tenant:
    referenceable: true
    fields:
        themeName:
            type: string
            nullable: true

Once your class is created, you can now put its FQCN into the MultiTenancy bundle's configuration:

.. code-block:: yaml

        # app/config/config.yml
        swp_multi_tenancy:
            persistence:
                phpcr:
                    enabled: true
                    # ..
                    classes:
                        tenant:
                            model: Acme\AppBundle\Document\Tenant

From now on your custom class will be used and you will be able to make use of the ``$themeName`` property in your app.

.. tip::

    See :doc:`/bundles/SWPMultiTenancyBundle/configuration` for more configuration details.

That's it, you can now refer to ``Acme\AppBundle\Document\Tenant`` to manage tenants in the PHPCR tree.
