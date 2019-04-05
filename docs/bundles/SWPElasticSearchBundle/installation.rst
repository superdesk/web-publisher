Installation
------------

Install the Bundle with Composer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In your project directory execute the following command to download the latest stable version:

.. code-block:: bash

    composer require swp/elastic-search-bundle

This command requires you to have Composer installed globally. If it's not installed `globally`_,
download the ``.phar`` file `locally`_ as explained in Composer documentation.

Install ElasticSearch
~~~~~~~~~~~~~~~~~~~~~

Install ElasticSearch server:

.. code-block:: bash

    brew install elasticsearch

Run ElasticSearch server:

.. code-block:: bash

    elasticsearch

Enable the bundle and its dependencies
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Enable the bundle and its dependencies
by adding the following lines in the ``app/AppKernel.php`` file:

.. code-block:: php

    // app/AppKernel.php

    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                new SWP\Bundle\CoreBundle\SWPCoreBundle(),
                // ...

                new FOS\ElasticaBundle\FOSElasticaBundle(),
                new SWP\Bundle\ElasticSearchBundle\SWPElasticSearchBundle(),
            );

            // ...
        }

        // ...
    }

.. note::

    Make sure you add it after SWPCoreBundle (``SWP\Bundle\CoreBundle\SWPCoreBundle()``)

Import config file in your `app/config/config.yml` file:

.. code-block:: yml

    imports:
        - { resource: "@SWPElasticSearchBundle/Resources/config/app/config.yml" }


Import routing file in `app/config/routing.yml`:

.. code-block:: yml

    swp_elasticsearch:
        resource: "@SWPElasticSearchBundle/Controller/Api"
        type:     annotation

Populate ElasticSearch server
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Run the below command to index all documents:

.. code-block:: bash

    bin/console fos:elastic:populate

That's it, the bundle is configured properly now!

.. _locally: https://getcomposer.org/doc/00-intro.md#locally
.. _globally: https://getcomposer.org/doc/00-intro.md#globally
