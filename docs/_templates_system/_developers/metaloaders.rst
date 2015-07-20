Meta Loaders
============

Meta Loaders are services injected into :code:`SWP\TemplateEngineBundle\Gimme\Loader\ChainLoader` class and used for loading specific types of Meta objects. 

Every Meta Loader must implement :code:`SWP\TemplateEngineBundle\Gimme\Loader\LoaderInterface` interface.

Required methods: 

* load
* isSupported


**Example Meta Loader**

.. code-block:: php

    <?php

    namespace SWP\TemplateEngineBundle\Gimme\Loader;

    use SWP\TemplateEngineBundle\Gimme\Meta\Meta;

    class ArticleLoader implements LoaderInterface
    {
        /**
         * @var string
         */
        protected $rootDir;

        /**
         * @param string $rootDir path to application root directory
         */
        public function __construct($rootDir)
        {
            $this->rootDir = $rootDir;
        }

        /**
         * Load meta object by provided type and parameters
         *
         * @MetaLoaderDoc(
         *     description="Article Meta Loader provide simple way to test Loader, it will be removed when real loaders will be merged.",
         *     parameters={}
         * )
         *
         * @param string $type         object type
         * @param array  $parameters   parameters needed to load required object type
         * @param int    $responseType response type: single meta (LoaderInterface::SINGLE) or collection of metas (LoaderInterface::COLLECTION)
         *
         * @return Meta|bool false if meta cannot be loaded, a Meta instance otherwise
         */
        public function load($type, $parameters, $responseType)
        {
            if ($responseType === LoaderInterface::SINGLE) {
                return new Meta($this->rootDir.'/Resources/meta/article.yml', array(
                    'title' => 'New article',
                    'keywords' => 'lorem, ipsum, dolor, sit, amet',
                    'don\'t expose it' => 'this should be not exposed',
                ));
            } else if ($responseType === LoaderInterface::COLLECTION) {
                return array(
                    new Meta($this->rootDir.'/Resources/meta/article.yml', array(
                        'title' => 'New article 1',
                        'keywords' => 'lorem, ipsum, dolor, sit, amet',
                        'don\'t expose it' => 'this should be not exposed',
                    )),
                    new Meta($this->rootDir.'/Resources/meta/article.yml', array(
                        'title' => 'New article 2',
                        'keywords' => 'lorem, ipsum, dolor, sit, amet',
                        'don\'t expose it' => 'this should be not exposed',
                    ))
                );
            }
        }

        /**
         * Checks if Loader supports provided type
         *
         * @param string $type
         *
         * @return boolean
         */
        public function isSupported($type)
        {
            return in_array($type, array('articles', 'article'));
        }
    }
