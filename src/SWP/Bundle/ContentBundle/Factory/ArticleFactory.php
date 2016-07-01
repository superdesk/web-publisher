<?php

namespace SWP\Bundle\ContentBundle\Factory;

use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class ArticleFactory implements ArticleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $baseFactory;

    /**
     * @var RouteProviderInterface
     */
    private $routeProvider;

    /**
     * @var ArticleProviderInterface
     */
    private $articleProvider;

    /**
     * @var string
     */
    private $contentRelativePath;

    /**
     * ArticleFactory constructor.
     *
     * @param FactoryInterface         $baseFactory
     * @param RouteProviderInterface   $routeProvider
     * @param ArticleProviderInterface $articleProvider
     * @param string                   $contentRelativePath
     */
    public function __construct(
        FactoryInterface $baseFactory,
        RouteProviderInterface $routeProvider,
        ArticleProviderInterface $articleProvider,
        $contentRelativePath
    ) {
        $this->baseFactory = $baseFactory;
        $this->routeProvider = $routeProvider;
        $this->articleProvider = $articleProvider;
        $this->contentRelativePath = $contentRelativePath;
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
    public function createFromPackage(PackageInterface $package)
    {
        $article = $this->create();
        $article->setParent($this->articleProvider->findOneById($this->contentRelativePath));
        $article->setTitle($package->getHeadline());
        $article->setBody(implode('', array_map(function (ItemInterface $item) {
            return $item->getBody();
        }, $package->getItems()->toArray())));
        $article->setLocale($package->getLanguage());
        $article->setRoute($this->routeProvider->getRouteForArticle($article));

        return $article;
    }
}
