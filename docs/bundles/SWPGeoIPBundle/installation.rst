Installation
------------

Install the Bundle with Composer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In your project directory execute the following command to download the latest stable version:

.. code-block:: bash

    composer require swp/geo-ip-bundle

This command requires you to have Composer installed globally. If it's not installed `globally`_,
download the ``.phar`` file `locally`_ as explained in Composer documentation.

Enable the bundle and its dependencies
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Enable the bundle by adding the following lines in the ``app/AppKernel.php`` file:

.. code-block:: php

    // app/AppKernel.php

    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(

                // ...
                new SWP\Bundle\GeoIPBundle\SWPGeoIPBundle(),
            );

            // ...
        }

        // ...
    }

.. note::

    All dependencies will be installed automatically.

That's it, the bundle is configured properly now!

.. _locally: https://getcomposer.org/doc/00-intro.md#locally
.. _globally: https://getcomposer.org/doc/00-intro.md#globally
