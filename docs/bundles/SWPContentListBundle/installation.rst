Installation
------------

Install the Bundle with Composer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In your project directory execute the following command to download the latest stable version:

.. code-block:: bash

    composer require swp/content-list-bundle

This command requires you to have Composer installed globally. If it's not installed `globally`_,
download the ``.phar`` file `locally`_ as explained in Composer documentation.

Enable the bundle and its dependencies
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Enable the bundle
by adding the following lines in the ``app/AppKernel.php`` file:

.. code-block:: php

    // app/AppKernel.php

    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                new SWP\Bundle\ContentListBundle\SWPStorageBundle(),
                new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
                // ...
                new SWP\Bundle\ContentListBundle\SWPContentListBundle(),
            );

            // ...
        }

        // ...
    }

.. note::

    All dependencies will be installed automatically. You will just need to configure the respective bundles if needed.

Configure the bundle
~~~~~~~~~~~~~~~~~~~~

.. configuration-block::

    .. code-block:: yaml

        # app/config/config.yml
        swp_content_list:
            persistence:
                orm:
                    # if true, ORM is enabled as a persistence backend
                    enabled: true

.. note::

    By default this bundle supports only Doctrine ORM as a persistence backend.

.. note::

    If this bundle is used together with :doc:`/bundles/SWPContentBundle/index`, configuration will be automatically pre-pended and enabled, so
    there is no need to configure it in your config file.

Configure Doctrine extensions which are used by this bundle:

.. code-block:: yaml

        # app/config/config.yml
        stof_doctrine_extensions:
            orm:
                default:
                    timestampable: true
                    softdeleteable: true
                    loggable: true

Using your custom list item content class:

.. configuration-block::

    .. code-block:: yaml

        # app/config/config.yml
        swp_content_list:
            persistence:
                orm:
                    # if true, ORM is enabled as a persistence backend
                    enabled: true
                    classes:
                        # ..
                        list_content:
                            model: Acme\MyBundle\Entity\Post

.. note::
    ``Acme\MyBundle\Entity\Post`` must implement ``SWP\Component\ContentList\Model\ListContentInterface`` interface.


Update Database Schema
~~~~~~~~~~~~~~~~~~~~~~

Run the following command:

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

That's it, the bundle is configured properly now!

.. _locally: https://getcomposer.org/doc/00-intro.md#locally
.. _globally: https://getcomposer.org/doc/00-intro.md#globally
