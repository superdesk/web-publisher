How to Provide Model Classes for several Doctrine Implementations
-----------------------------------------------------------------

When building a bundle that could be used not only with Doctrine ORM but also the CouchDB ODM, MongoDB ODM or PHPCR ODM, you should still only write one model class. The Doctrine bundles provide a compiler pass to register the mappings for your model classes. This bundle helps you easily register these mappings.

.. note::

    See Symfony `documentation`_ for more details.

Let's say you created a new bundle called **ContentBundle** and you want to register your model classes mappings.
You could do that in a normal way by defining it in the config as follows:

.. code-block:: yaml

    # app/config/config.yml

    doctrine:
        # ..
        orm:
            entity_managers:
                default:
                    # ..
                    auto_mapping: false
                    mappings:
                        AcmeContentBundle:
                            type: yml
                            prefix: Acme\ContentBundle\Model
                            dir: Resources/config/doctrine

but you want to have possibility and a flexible way to register your model classes mappings both for Doctrine ORM and PHPCR ODM using single model class (let's use ``Acme\ContentBundle\Model\Article`` model class as a example).
To achieve that, all you need to do is extend your ``AcmeContentBundle`` class with
``SWP\Bundle\StorageBundle\DependencyInjection\Bundle\Bundle`` from ``StorageBundle``.

.. note::

    By default YAML mappings files are loaded. You can change it by setting the ``$mappingFormat`` property to:
    ``protected $mappingFormat = BundleInterface::MAPPING_XML;`` (see ``SWP\Component\Storage\Bundle\BundleInterface``)
    if you wish to load mapping files in XML format.
    SWPStorageBundle is able to load mappings in xml, yml and annotation formats.

Example:

.. code-block:: php

    // src/Acme/ContentBundle/AcmeContentBundle.php
    namespace Acme\ContentBundle\AcmeContentBundle;

    use SWP\Bundle\StorageBundle\DependencyInjection\Bundle\Bundle;
    use SWP\Bundle\StorageBundle\Drivers;
    use Symfony\Component\DependencyInjection\ContainerBuilder;

    class AcmeContentBundle extends Bundle
    {
        /**
         * {@inheritdoc}
         */
        public function getSupportedDrivers()
        {
            return [
                Drivers::DRIVER_DOCTRINE_ORM,
                Drivers::DRIVER_DOCTRINE_PHPCR_ODM,
            ];
        }

        /**
         * {@inheritdoc}
         */
        public function getModelClassNamespace()
        {
            return 'Acme\\ContentBundle\\Model';
        }
    }

Your bundle can support now multiple drivers.
According to the example above, your ``Article`` model class namespace is specified in ``getModelClassNamespace()`` method.
Two drivers are configured: PHPCR and ORM. In this case you can create model class for PHPCR and ORM and extend the default
``Acme\ContentBundle\Model\Article`` class. You would then have different implementations for PHPCR and ORM using the same
model class:

 - ``Acme\ContentBundle\ODM\PHPCR\Article`` should extend ``Acme\ContentBundle\Model\Article``
 - ``Acme\ContentBundle\ORM\Article`` should extend ``Acme\ContentBundle\Model\Article``


All what you need to do now is to place the mapping files for each model classes.
In this case the ``Acme\ContentBundle\ODM\PHPCR\Article`` class mapping should be placed inside ``Resources/config/doctrine-phpcr`` directory. The mappings for ORM classes should be placed inside ``Resources/config/doctrine-orm`` directory.

.. note::

  A reference to the directories where mapping files should be placed for model classes are generated automatically,
  based on the supported driver. In case of PHPCR it will be ``Resources/config/doctrine-phpcr`` and in case of Doctrine ORM it will be ``Resources/config/doctrine-orm``. These directories should be created manually if don't exist.

The ``getSupportedDrivers`` defines supported drivers by the ``ContentBundle`` e.g. PHPCR ODM, MongoDB ODM etc.
You should use ``SWP\Bundle\StorageBundle\Drivers`` class to specify the supported drivers as shown in the example above.

The ``Drivers`` class provide drivers' constants:

.. code-block:: php

    namespace SWP\Bundle\StorageBundle;

    class Drivers
    {
        const DRIVER_DOCTRINE_ORM = 'orm';
        const DRIVER_DOCTRINE_MONGODB_ODM = 'mongodb';
        const DRIVER_DOCTRINE_PHPCR_ODM = 'phpcr';
    }

.. _documentation: http://symfony.com/doc/current/cookbook/doctrine/mapping_model_classes.html
