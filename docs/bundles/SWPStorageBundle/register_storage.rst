How to automatically register Services required by the configured Storage Driver
--------------------------------------------------------------------------------

This bundle allows to register required services on the basis of the configured storage driver to standardize the definitions of registered services.

By default, this bundle registers:

- repository services
- factory services
- object manager services
- parameters

Based on the provided model class it will register default factory and repository,
where you will be able to create new object based on provided model class name, adding, removing objects from the repository.


Set Configuration for your Bundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's first start from your bundle configuration where you will need to specify default configuration.
In this example let's assume you already have a bundle called ``ContentBundle`` and you want to have working
services to manage your resources.

The default ``Configuration`` class would look something like that:

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


Now, let's add the needed configuration where you would configure ``Acme\ContentBundle\ODM\PHPCR\Article`` model class for PHPCR driver:

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

    The ``repository``,  ``factory`` and ``object_manager_name`` nodes are configured to use ``null`` as default value. It means that the default factory, repository and object manager services will be registered in the container.

Register configured classes in your Extension class
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Now, that you have the configuration defined, it is time to register those classes using ``Extension`` class in your bundle. By default such class is generated inside the ``DependencyInjection`` folder in every Symfony Bundle.
In this ``ContentBundle`` example it will be located under the ``Acme\ContentBundle\DependencyInjection`` namespace.
The fully qualified class name will be ``Acme\ContentBundle\DependencyInjection\AcmeContentExtension``.
You need to extend this class by ``SWP\Bundle\StorageBundle\DependencyInjection\Extension\Extension`` class which
will give you an access to register configured classes needed by the storage. ``registerStorage`` method
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

If the PHPCR persistence backend is enabled it will register the following services in the container:

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

If your configuration would support Doctrine ORM instead of PHPCR, the default services definitions would be:

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

You could then access parameters from the container as visible below:

.. code-block:: php

    <?php
    //..
    $className = $container->getParameter('swp.model.article.class');
    var_dump($className); // will return Acme\ContentBundle\PHPCR\Article

All what you would need to do now is to enable the configuration in your config file to register all classes:

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



How to create and use custom factory service for your model
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

For some use cases you would need to implement your own methods in the repository, like ``findOneBySlug()`` or
``findAllArticles()``.

It's very easy.
You need to create your custom implementation fo the repository. In this example you will create custom repository
for ``Article`` model class and Doctrine PHPCR persistence backend.
Firstly, you need to create your custom repository interface, let's name it ``ArticleRepositoryInterface`` and extend it
by ``SWP\Component\Storage\Repository\RepositoryInterface`` interface:

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


Secondly, you need to create your custom repository class, let's name it ``ArticleRepository`` and implement
``ArticleRepositoryInterface`` interface:

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

    If you want to create custom repository for Doctrine ORM persistence backend, you need to extend your custom
    repository class by ``SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository`` class.

The last step is to add your custom repository to the configuration in config file:

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

    You can change repository class by simply changing your bundle configuration, without a need to change the code.

How to create and use custom repository service for your model
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You may need to have a different way of creating objects than the default way of doing it.
Imagine you need to create ``Article`` object with the route assigned by default.

.. note::

    In this example you will create custom factory for your ``Article`` object and Doctrine PHPCR persistence backend.

Let's create custom interface for your factory. Extend your custom class by ``SWP\Component\Storage\Factory\FactoryInterface`` class:

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


Create custom Article factory class:

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

Create compiler pass to override default Article factory class with your custom factory on container compilation:

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


The last thing which is required to make use of your new factory service is to add it to the configuration
in your config file under the ``factory`` node:

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


You would be able to use the factory like:

.. code-block:: php

    $article = $this->get('swp.factory.article')->createWithRoute('some-route');
    // or create flat object
    $article = $this->get('swp.factory.article')->create();


.. note::

    You can change factory class by simply changing your bundle configuration, without a need to change the code.


Configuring object manager for your model
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

As you can see there is ``object_manager_name`` option in ``Configuration`` class, which is the default Object Manager (Contract for a Doctrine persistence layer) name.

In case of Doctrine ORM it's ``doctrine.orm.default_entity_manager``, in PHPCR it's ``doctrine_phpcr.odm.default_document_manager``. If you set this option to be, for example, ``test`` the ``doctrine.orm.test_entity_manager`` object manager service's id will be used. Of course this new ``test`` document in case of PHPCR should be first configured in Doctrine PHPCR Bundle as described in the `bundle documentation`_ and for Doctrine ORM it should be
configured in Doctrine ORM Bundle config as described `here`_.

To have the possibility of defining default Object Manager for a Doctrine persistence layer and make use of it
in the registered repositories and factories in your Bundle, is very useful in case you are using different databases or even different
sets of entities.

.. note::

    Factories and repositories are defined as a services in Symfony container to have better flexibility of using it.

.. _bundle documentation: http://symfony.com/doc/current/cmf/bundles/phpcr_odm/multiple_sessions.html#multiple-document-managers
.. _here: http://symfony.com/doc/current/cookbook/doctrine/multiple_entity_managers.html