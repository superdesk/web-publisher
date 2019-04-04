Installation
------------

Install the Bundle with Composer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In your project directory, execute the following command to download the latest stable version of the MultiTenancyBundle:

.. code-block:: bash

    composer require swp/multi-tenancy-bundle

This command requires you to have Composer installed globally. If it's not installed `globally`_,
download the ``.phar`` file `locally`_ as explained in Composer documentation.

Enable the bundle and its dependencies
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. note::

    By default Jackalope Doctrine DBAL is required for PHPCR ODM in this bundle.
    See `Choosing a PHPCR Implementation for alternatives`_.


Enable the bundle and its dependencies (`DoctrinePHPCRBundle`_, `DoctrineBundle`_)
by adding the following lines in the ``app/AppKernel.php`` file:

.. code-block:: php

    // app/AppKernel.php

    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...

                new Doctrine\Bundle\PHPCRBundle\DoctrinePHPCRBundle(),
                new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
                new SWP\MultiTenancyBundle\SWPMultiTenancyBundle(),
            );

            // ...
        }

        // ...
    }

.. note::

    All dependencies will be installed automatically. You will just need to configure the respective bundles.


Configure the SWPMultiTenancyBundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's enable PHPCR persistence backend.

.. configuration-block::

    .. code-block:: yaml

        # app/config/config.yml
        swp_multi_tenancy:
            persistence:
                phpcr:
                    # if true, PHPCR is enabled in the service container
                    enabled: true

    .. code-block:: xml

        <!-- app/config/config.xml -->
        <?xml version="1.0" encoding="UTF-8" ?>
        <swp_multi_tenancy>
            <persistence>
                <phpcr>
                    <!-- if true, PHPCR is enabled in the service container -->
                    <enabled>true</enabled>
                </phpcr>
            </persistence>
        </swp_multi_tenancy>

.. tip::

    See :doc:`/bundles/SWPMultiTenancyBundle/tutorials/routing_integration`.

.. note::

    See :doc:`/bundles/SWPMultiTenancyBundle/configuration` for more details.


DoctrinePHPCRBundle Configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

`See how to set PHPCR Session Configuration`_.

Add the domain parameter
~~~~~~~~~~~~~~~~~~~~~~~~

Add the following parameter to your parameters file, so the current tenant can be resolved and matched against
the configured domain.

.. code-block:: yaml

        # app/config/parameters.yml
        domain: example.com


Update your database schema
~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. note::

  This step assumes you have already configured and created the database.

Execute the following commands in the console:

.. code-block:: bash

    php bin/console doctrine:schema:update --force
    php bin/console doctrine:phpcr:repository:init
    php bin/console swp:organization:create --default
    php bin/console swp:tenant:create --default
    php bin/console doctrine:phpcr:repository:init

That's it, the bundle is configured properly now!

.. _locally: https://getcomposer.org/doc/00-intro.md#locally
.. _globally: https://getcomposer.org/doc/00-intro.md#globally
.. _DoctrineBundle: https://github.com/doctrine/DoctrineBundle
.. _DoctrinePHPCRBundle: https://github.com/doctrine/DoctrinePHPCRBundle
.. _Choosing a PHPCR Implementation for alternatives: http://symfony.com/doc/master/cmf/cookbook/database/choosing_phpcr_implementation.html
.. _See how to set PHPCR Session Configuration: http://symfony.com/doc/master/cmf/bundles/phpcr_odm/introduction.html#phpcr-session-configuration
