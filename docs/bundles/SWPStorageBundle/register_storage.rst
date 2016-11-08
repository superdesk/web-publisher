How to automatically register Services required by the configured Storage Driver
--------------------------------------------------------------------------------

This bundle enables you to register required services on the basis of the configured storage driver, to standardize the definitions of registered services.

By default, this bundle registers:

- repository services
- factory services
- object manager services
- parameters

Based on the provided model class it will register the default factory and repository,
where you will be able to create a new object based on the provided model class name, adding or removing objects from the repository.


Set Configuration for your Bundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's start from your bundle configuration, where you will need to specify the default configuration.
In this example let's assume you already have a bundle called ``ContentBundle`` and you want to have working
services to manage your resources.

The default ``Configuration`` class would look something like this:

.. code-block:: php

    <?php

    namespace Acme\ContentBundle\DependencyInjection;

    use Symfony\Component\Config\Definition\Builder\TreeBuilder;
    use Symfony\Component\Config\Definition\ConfigurationInterface;

    class Configuration implements ConfigurationInterface
    {
        /**
         * {@inheritdoc}
         */
        public function getConfigTreeBuilder()
        {
            $treeBuilder = new TreeBuilder();
            $treeBuilder->root('acme_content');

            return $treeBuilder;
        }
    }


Now, let's add the configuration for the ``Acme\ContentBundle\ODM\PHPCR\Article`` model class, for the PHPCR driver:

.. code-block:: php

    <?php

    namespace Acme\ContentBundle\DependencyInjection;
    // ..

    use Acme\ContentBundle\ODM\PHPCR\Article;

    class Configuration implements ConfigurationInterface
    {
        /**
         * {@inheritdoc}
         */
        public function getConfigTreeBuilder()
        {
            $treeBuilder = new TreeBuilder();
            $treeBuilder->root('acme_content')
                ->children()
                    ->arrayNode('persistence')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('phpcr')
                                ->addDefaultsIfNotSet()
                                ->canBeEnabled()
                                ->children()
                                    ->arrayNode('classes')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->arrayNode('article')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('model')->cannotBeEmpty()->defaultValue(Article::class)->end()
                                                    ->scalarNode('repository')->defaultValue(null)->end()
                                                    ->scalarNode('factory')->defaultValue(null)->end()
                                                    ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end() // phpcr
                        ->end()
                    ->end()
                ->end();

            return $treeBuilder;
        }
    }

.. note::

    The ``repository``,  ``factory`` and ``object_manager_name`` nodes are configured to use ``null`` as the default value. It means that the default factory, repository and object manager services will be registered in the container.

Register configured classes in your Extension class
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Now that you have the configuration defined, it is time to register those classes using the ``Extension`` class in your bundle.
By default, this class is generated inside the ``DependencyInjection`` folder in every Symfony Bundle.

In this ``ContentBundle`` example it will be located under the namespace ``Acme\ContentBundle\DependencyInjection``.
The fully qualified class name will be ``Acme\ContentBundle\DependencyInjection\AcmeContentExtension``.

You need to extend this class by the ``SWP\Bundle\StorageBundle\DependencyInjection\Extension\Extension`` class, which
will give you access to register configured classes needed by the storage. The ``registerStorage`` method
will do the whole magic for you. See the code below:

.. code-block:: php

    <?php

    namespace Acme\ContentBundle\DependencyInjection;

    // ..
    use SWP\Bundle\StorageBundle\Drivers;
    use SWP\Bundle\StorageBundle\DependencyInjection\Extension\Extension;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\Config\FileLocator;
    use Symfony\Component\DependencyInjection\Loader;

    class AcmeContentExtension extends Extension
    {
        /**
         * {@inheritdoc}
         */
        public function load(array $configs, ContainerBuilder $container)
        {
            $config = $this->processConfiguration(new Configuration(), $configs);
            $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('services.yml');

            if ($config['persistence']['phpcr']['enabled']) {
                $this->registerStorage(Drivers::DRIVER_DOCTRINE_PHPCR_ODM, $config['persistence']['phpcr'], $container);
            }
        }
    }

If the PHPCR persistence backend is enabled, it will register the following services in the container:

+-----------------------------+----------------------------------------------------------------------+
| Service ID                  | Class name                                                           |
+=============================+======================================================================+
| swp.factory.article         | SWP\\Bundle\\StorageBundle\\Factory\\Factory                         |
+-----------------------------+----------------------------------------------------------------------+
| swp.repository.article.class  | Acme\\ContentBundle\\PHPCR\\Article                                |
+-----------------------------+----------------------------------------------------------------------+
| swp.repository.article      | SWP\\Bundle\\StorageBundle\\Doctrine\\ODM\\PHPCR\\DocumentRepository |
+-----------------------------+----------------------------------------------------------------------+

together with all parameters:

+-----------------------------+----------------------------------------------------------------------+
| Parameter Name              | Value                                                                |
+=============================+======================================================================+
| swp.factory.article.class   | SWP\\Bundle\\StorageBundle\\Factory\\Factory                         |
+-----------------------------+----------------------------------------------------------------------+
| swp.model.article.class     | Acme\\ContentBundle\\PHPCR\\Article                                  |
+-----------------------------+----------------------------------------------------------------------+
| swp.repository.article.class| SWP\\Bundle\\StorageBundle\\Doctrine\\ODM\\PHPCR\\DocumentRepository |
+-----------------------------+----------------------------------------------------------------------+

If your configuration supports Doctrine ORM instead of PHPCR, the default service definitions would be:

+-----------------------------+----------------------------------------------------------------------+
| Service ID                  | Class name                                                           |
+=============================+======================================================================+
| swp.factory.article         | SWP\\Bundle\\StorageBundle\\Factory\\Factory                         |
+-----------------------------+----------------------------------------------------------------------+
| swp.object_manager.article  | alias for "doctrine.orm.default_entity_manager"                      |
+-----------------------------+----------------------------------------------------------------------+
| swp.repository.article      | SWP\\Bundle\\StorageBundle\\Doctrine\\ORM\\EntityRepository          |
+-----------------------------+----------------------------------------------------------------------+

And all parameters in the container would look like:

+-----------------------------+----------------------------------------------------------------------+
| Parameter Name              | Value                                                                |
+=============================+======================================================================+
| swp.factory.article.class   | SWP\\Bundle\\StorageBundle\\Factory\\Factory                         |
+-----------------------------+----------------------------------------------------------------------+
| swp.model.article.class     | Acme\\ContentBundle\\ORM\\Article                                    |
+-----------------------------+----------------------------------------------------------------------+
| swp.repository.article.class| SWP\\Bundle\\StorageBundle\\Doctrine\\ORM\\EntityRepository          |
+-----------------------------+----------------------------------------------------------------------+

You could then access parameters from the container, as visible below:

.. code-block:: php

    <?php
    //..
    $className = $container->getParameter('swp.model.article.class');
    var_dump($className); // will return Acme\ContentBundle\PHPCR\Article

Now, register all classes in the configuration file:

.. code-block:: yaml

    # app/config/config.yml
    swp_content:
        persistence:
            phpcr: true

The above configuration is equivalent to:

.. code-block:: yaml

    # app/config/config.yml
    swp_content:
        persistence:
            phpcr:
                enabled: true
                classes:
                    article:
                        model: Acme\ContentBundle\ODM\PHPCR\Article
                        factory: ~
                        repository: ~
                        object_manager_name: ~



How to create and use custom repository service for your model
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

For some use cases you would need to implement your own methods in the repository, like ``findOneBySlug()`` or
``findAllArticles()``. It's very easy!

You need to create your custom implementation for the repository. In this example you will create a custom repository
for the ``Article`` model class and Doctrine PHPCR persistence backend.

Firstly, you need to create your custom repository interface. Let's name it ``ArticleRepositoryInterface`` and extend it
by the ``SWP\Component\Storage\Repository\RepositoryInterface`` interface:

.. code-block:: php

    <?php

    namespace Acme\ContentBundle\PHPCR;

    use Acme\ContentBundle\Model\ArticleInterface;
    use SWP\Component\Storage\Repository\RepositoryInterface;

    interface ArticleRepositoryInterface extends RepositoryInterface
    {
        /**
         * Find one article by slug.
         *
         * @param string $slug
         *
         * @return ArticleInterface
         */
        public function findOneBySlug($slug);

        /**
         * Find all articles.
         *
         * @return mixed
         */
        public function findAllArticles();
    }


Secondly, you need to create your custom repository class. Let's name it ``ArticleRepository`` and implement
the ``ArticleRepositoryInterface`` interface:

.. code-block:: php

    <?php

    namespace Acme\ContentBundle\PHPCR;

    use Acme\ContentBundle\Model\ArticleRepositoryInterface;
    use SWP\Bundle\StorageBundle\Doctrine\ODM\PHPCR\DocumentRepository;

    class ArticleRepository extends DocumentRepository implements ArticleRepositoryInterface
    {
        /**
         * {@inheritdoc}
         */
        public function findOneBySlug($slug)
        {
            return $this->findOneBy(['slug' => $slug]);
        }

        /**
         * {@inheritdoc}
         */
        public function findAllArticles()
        {
            return $this->createQueryBuilder('o')->getQuery();
        }
    }

.. note::

    If you want to create a custom repository for the Doctrine ORM persistence backend, you need to extend your custom
    repository class by the ``SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository`` class.

The last step is to add your custom repository to the configuration file:

.. code-block:: yaml

    # app/config/config.yml
    swp_content:
        persistence:
            phpcr:
                enabled: true
                classes:
                    article:
                        model: Acme\ContentBundle\ODM\PHPCR\Article
                        factory: ~
                        repository: Acme\ContentBundle\PHPCR\ArticleRepository
                        object_manager_name: ~

.. note::

    Alternatively, you could add it directly in your ``Configuration`` class.

.. note::

    You can change repository class by simply changing your bundle configuration, without needing to change the code.

How to create and use custom factory service for your model
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You may need to have a different way of creating objects than the default way of doing it.
Imagine you need to create an ``Article`` object with the route assigned by default.

.. note::

    In this example you will create a custom factory for your ``Article`` object and Doctrine PHPCR persistence backend.

Let's create a custom interface for your factory. Extend your custom class by the ``SWP\Component\Storage\Factory\FactoryInterface`` class:

.. code-block:: php

    <?php

    namespace Acme\ContentBundle\Factory;

    use SWP\Bundle\ContentBundle\Model\ArticleInterface;
    use SWP\Component\Bridge\Model\PackageInterface;
    use SWP\Component\Storage\Factory\FactoryInterface;

    interface ArticleFactoryInterface extends FactoryInterface
    {
        /**
         * Create a new object with route.
         *
         * @param string $route
         *
         * @return ArticleInterface
         */
        public function createWithRoute($route);
    }


Create the custom Article factory class:

.. code-block:: php

    <?php

    namespace Acme\ContentBundle\Factory;

    use SWP\Component\Storage\Factory\FactoryInterface;

    class ArticleFactory implements ArticleFactoryInterface
    {
        /**
         * @var FactoryInterface
         */
        private $baseFactory;

        /**
         * ArticleFactory constructor.
         *
         * @param FactoryInterface $baseFactory
         */
        public function __construct(FactoryInterface $baseFactory)
        {
            $this->baseFactory = $baseFactory;
        }

        /**
         * {@inheritdoc}
         */
        public function create()
        {
            return $this->baseFactory->create();
        }

        /**
         * {@inheritdoc}
         */
        public function createWithRoute($route)
        {
            $article = $this->create();
            // ..
            $article->setRoute($route);

            return $article;
        }
    }

Create a compiler pass to override the default Article factory class with your custom factory on container compilation:

.. code-block:: php

    <?php

    namespace Acme\ContentBundle\DependencyInjection\Compiler;

    use SWP\Component\Storage\Factory\Factory;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
    use Symfony\Component\DependencyInjection\Definition;
    use Symfony\Component\DependencyInjection\Parameter;

    class RegisterArticleFactoryPass implements CompilerPassInterface
    {
        /**
         * {@inheritdoc}
         */
        public function process(ContainerBuilder $container)
        {
            if (!$container->hasDefinition('swp.factory.article')) {
                return;
            }

            $baseDefinition = new Definition(
                Factory::class,
                [
                    new Parameter('swp.model.article.class'),
                ]
            );

            $articleFactoryDefinition = new Definition(
                $container->getParameter('swp.factory.article.class'),
                [
                    $baseDefinition,
                ]
            );

            $container->setDefinition('swp.factory.article', $articleFactoryDefinition);
        }
    }


Don't forget to register your new compiler pass in your Bundle class (``AcmeContentBundle``):

.. code-block:: php

    <?php

    use Acme\ContentBundle\DependencyInjection\Compiler\RegisterArticleFactoryPass;
    // ..

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegisterArticleFactoryPass());
    }


The last thing required to make use of your new factory service is to add it to the configuration file, under the ``factory`` node:

.. code-block:: yaml

    # app/config/config.yml
    swp_content:
        persistence:
            phpcr:
                enabled: true
                classes:
                    article:
                        model: Acme\ContentBundle\ODM\PHPCR\Article
                        factory: Acme\ContentBundle\Factory\ArticleFactory
                        repository: ~
                        object_manager_name: ~

.. note::

    Alternatively, you could add it directly in your ``Configuration`` class.

You would then be able to use the factory like so:

.. code-block:: php

    $article = $this->get('swp.factory.article')->createWithRoute('some-route');
    // or create flat object
    $article = $this->get('swp.factory.article')->create();

.. note::

    You can change factory class by simply changing your bundle configuration, without needing to change the code.


Configuring object manager for your model
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

As you can see, there is the ``object_manager_name`` option in the ``Configuration`` class, which is the default Object Manager (Contract for a Doctrine persistence layer) name.

In the case of Doctrine ORM it's ``doctrine.orm.default_entity_manager``, in PHPCR it's ``doctrine_phpcr.odm.default_document_manager``.

If you set this option to be, for example, ``test`` the ``doctrine.orm.test_entity_manager`` object manager service's id will be used. Of course this new ``test`` document, in the case of PHPCR, should be first configured in the Doctrine PHPCR Bundle as described in the `bundle documentation`_ on multiple document managers.
For Doctrine ORM it should be configured as shown in the Doctrine ORM Bundle documentation on `multiple entity managers`_.

The possibility of defining a default Object Manager for a Doctrine persistence layer, and making use of it in the registered repositories and factories in your Bundle, is very useful in case you are using different databases or even different sets of entities.

.. note::

    Factories and repositories are defined as a services in Symfony container to have better flexibility of use.

.. _bundle documentation: http://symfony.com/doc/master/cmf/bundles/phpcr_odm/multiple_sessions.html#multiple-document-managers
.. _multiple entity managers: http://symfony.com/doc/current/cookbook/doctrine/multiple_entity_managers.html

Resolve target entities
-----------------------

This chapter is strictly related to `How to Define Relationships with Abstract Classes and Interfaces`_ so please read it first.

.. _How to Define Relationships with Abstract Classes and Interfaces: http://symfony.com/doc/current/doctrine/resolve_target_entity.html

This functionality allows you to define relationships between different entities without making them hard dependencies. All you need to do is to define ``interface`` node in your bundle's `Configuration` class.

See example below:

.. code-block:: php

    <?php

    namespace Acme\CoreBundle\DependencyInjection;
    // ..

    use Acme\Component\MultiTenancy\Model\Tenant;
    use Acme\Component\MultiTenancy\Model\TenantInterface;
    use Acme\Component\MultiTenancy\Model\Organization;
    use Acme\Component\MultiTenancy\Model\OrganizationInterface;

    class Configuration implements ConfigurationInterface
    {
        /**
         * {@inheritdoc}
         */
        public function getConfigTreeBuilder()
        {
            $treeBuilder = new TreeBuilder();
            $treeBuilder->root('acme_core')
                ->children()
                    ->arrayNode('persistence')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('phpcr')
                                ->addDefaultsIfNotSet()
                                ->canBeEnabled()
                                ->children()
                                    ->arrayNode('classes')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->arrayNode('tenant')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('model')->cannotBeEmpty()->defaultValue(Tenant::class)->end()
                                                    ->scalarNode('interface')->cannotBeEmpty()->defaultValue(TenantInterface::class)->end()
                                                    ->scalarNode('repository')->defaultValue(null)->end()
                                                    ->scalarNode('factory')->defaultValue(null)->end()
                                                    ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                                ->end()
                                            ->end()
                                            ->arrayNode('organization')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('model')->cannotBeEmpty()->defaultValue(Organization::class)->end()
                                                    ->scalarNode('interface')->cannotBeEmpty()->defaultValue(OrganizationInterface::class)->end()
                                                    ->scalarNode('repository')->defaultValue(null)->end()
                                                    ->scalarNode('factory')->defaultValue(null)->end()
                                                    ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end() // phpcr
                        ->end()
                    ->end()
                ->end();

            return $treeBuilder;
        }
    }

In this case you will be able to specify your interface for your model via config file:

.. code-block:: yaml

    # app/config/config.yml
    swp_content:
        persistence:
            phpcr:
                enabled: true
                classes:
                    tenant:
                        model: Acme\AppBundle\Model\Tenant # extends default Acme\Component\MultiTenancy\Model\Tenant class
                        interface: ~
                        # ..
                    organization:
                        model: Acme\AppBundle\Model\Organization # extends default Acme\Component\MultiTenancy\Model\Organization class
                        interface: ~
                        # ..

Now, no mather which model you will use in your bundle's configuration above, the interface will be automatically resolved to defined entity and will be used by your mapping file without a need to change any extra code or configuration setup.

The above is equivalent to if the Tenant has a relation to Organization and vice versa.

.. code-block:: yaml

    # app/config/config.yml
    doctrine:
        # ...
        orm:
            # ...
            resolve_target_entities:
                Acme\Component\MultiTenancy\Model\OrganizationInterface: Acme\Bundle\CoreBundle\Model\Organization
                Acme\Component\MultiTenancy\Model\TenantInterface: Acme\Bundle\CoreBundle\Model\Tenant

In this example above every time you will want to change your model inside your bundle's configuration you would also need to care about the Doctrine config as the specified entity will not change automatically to a new one which was defined in bundle's config.

Inheritance Mapping
-------------------

By default every entity inside bundle should be mapped as `Mapped superclass`_. This bundle helps you manage and simplify inheritance mapping in case you want to use default mapping or extend it. In this case the following applies:

- If you do not configure your custom class, the default mapped superclasses become entites.
- Otherwise they become mapped superclasses and move the conflicting mappings (these which you cannot normally configure on mapped superclass) to your class mapping. For example, you do not need anymore to map Organization -> Tenants inside your custom class, it is copied transparently from the bundle.
- It also works on all levels, so you can cleanly override the core bundle models! If you configure other class than core one, your entity will be used and the core model will remain mapped superclass.

.. note::

    This feature and its description has been ported from Sylius project. See `related issue`_.

.. _Mapped superclass: http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/inheritance-mapping.html#mapped-superclasses
.. _related issue: https://github.com/Sylius/Sylius/issues/221
