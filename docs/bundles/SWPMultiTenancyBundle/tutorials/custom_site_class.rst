Creating a custom Site document
~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. note::

  This bundle requires to have CMF RoutingBundle, DoctrinePHPCRBundle installed,
  see :doc:`/bundles/SWPMultiTenancyBundle/tutorials/routing_integration`.


If needed, you can provide your own Site document class to represents the tenant in PHPCR tree.
This new class must implement :ref:`bundle_tenant_site_interface`
which is provided by this bundle or you can extend the default :ref:`bundle_tenant_model_site` class.

.. code-block:: php

    <?php

    use Acme\AppBundle\Document\Site as BaseSite;

    class Site extends BaseSite
    {
        // ..
        public function getHomepage()
        {
            return $this->homepage;
        }

        public function setHomepage(Route $homepage)
        {
            $this->homepage = $homepage;
        }
    }

Once your class is created, you can now put its FQCN into the bundle configuration:

.. code-block:: yaml

        # app/config/config.yml
        swp_multi_tenancy:
            persistence:
                phpcr:
                    enabled: true
                    # ..
                    site_document_class: Acme\AppBundle\Document\Site

.. tip::

    See :doc:`/bundles/SWPMultiTenancyBundle/configuration` for more configuration details.

That's it, you can now refer to ``Acme\AppBundle\Document\Site`` to manage tenants in PHPCR tree.
