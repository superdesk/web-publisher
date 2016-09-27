Installation
------------

Install the Bundle with Composer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In your project directory execute the following command to download the latest stable version:

.. code-block:: bash

    composer require swp/rule-bundle

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
                new Burgov\Bundle\KeyValueFormBundle\BurgovKeyValueFormBundle(),
                new SWP\Bundle\BridgeBundle\SWPStorageBundle()
                // ...
                new SWP\Bundle\BridgeBundle\SWPRuleBundle()
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
        swp_rule:
            persistence:
                orm:
                    # if true, ORM is enabled as a persistence backend
                    enabled: true

.. note::

    By default this bundle supports only Doctrine ORM as a persistence backend.

Update Database Schema
~~~~~~~~~~~~~~~~~~~~~~

Run the following command:

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

That's it, the bundle is configured properly now!

.. _locally: https://getcomposer.org/doc/00-intro.md#locally
.. _globally: https://getcomposer.org/doc/00-intro.md#globally
