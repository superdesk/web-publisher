Installation
------------

Install the Bundle with Composer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Open a command console and in your project directory execute the
following command to download the latest stable version:

.. code-block:: bash

    $ composer require swp/multi-tenancy-bundle

This command requires you to have Composer installed globally. If it's not installed `globally`_,
download the ``.phar`` file `locally`_ as explained in Composer documentation.

Enable the bundle and its dependencies
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Enable the bundle and its dependencies (`StofDoctrineExtensionsBundle`_, `DoctrineBundle`_)
by adding the following lines in the ``app/AppKernel.php``:

.. code-block:: php

    // app/AppKernel.php

    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...

                new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
                new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
                new SWP\MultiTenancyBundle\SWPMultiTenancyBundle(),
            );

            // ...
        }

        // ...
    }

.. note::

    All dependencies will be installed automatically. You will just need to configure the respective bundles.


Configure the SWPMultiTenancyBundle (optional)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This bundle by default works without any configuration.
When using PHPCR-ODM, the configuration needs to be provided.

Add the following configuration if you are using PHPCR ODM:

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

Configure the StofDoctrineExtensionsBundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Some more steps needs to be performed here in order to fully make use of the extensions.

Enable Doctrine extension in your config file
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Enable needed extensions by adding the configuration below to your config file.

.. code-block:: yaml

        # app/config/config.yml
        stof_doctrine_extensions:
            orm:
                default:
                    # updates date fields on create, update and even property change.
                    timestampable: true
                    # allows to implicitly remove records
                    softdeleteable: true
                    # helps tracking changes and history of objects, also supports version management
                    loggable: true


Add the extensions to your mapping
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Loggable extension needs its default entity to be configured in order to work properly.
Register its mapping in Doctrine by adding the following configuration to your config file.

.. code-block:: yaml

        # app/config/config.yml
        doctrine:
            orm:
                entity_managers:
                    default:
                        mappings:
                            gedmo_loggable:
                                type: annotation
                                prefix: Gedmo\Loggable\Entity
                                dir: "%kernel.root_dir%/../vendor/gedmo/doctrine-extensions/lib/Gedmo/Loggable/Entity"
                                is_bundle: false

.. note::

  If you are using the short syntax for the ORM configuration, the mappings key is directly under ``orm:``


Enable SoftDeleteableFilter
~~~~~~~~~~~~~~~~~~~~~~~~~~~

To make use of SoftDeleteable behavior, you need to enable the Doctrine ORM filter.

.. code-block:: yaml

        # app/config/config.yml
        doctrine:
            orm:
                entity_managers:
                    default:
                        filters:
                            softdeleteable:
                                class: Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter
                                enabled: true

.. note::

  If you are using the short syntax for the ORM configuration, the `filters` key is directly under `orm:`

Add the domain parameter
~~~~~~~~~~~~~~~~~~~~~~~~

Add the following parameter to your parameters file, so the current tenant can be resolved and matched against
configured domain.

.. code-block:: yaml

        # app/config/parameters.yml
        domain: example.com


Update your database schema
~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. note::

  This step assumes you have already the database configured and created.

Execute the following commands in terminal:

.. code-block:: bash

    php app/console doctrine:schema:update --force
    php app/console doctrine:phpcr:repository:init


That's it, the bundle is configured properly now!

.. _locally: https://getcomposer.org/doc/00-intro.md#locally
.. _globally: https://getcomposer.org/doc/00-intro.md#globally
.. _StofDoctrineExtensionsBundle: https://github.com/stof/StofDoctrineExtensionsBundle
.. _DoctrineBundle: https://github.com/doctrine/DoctrineBundle
