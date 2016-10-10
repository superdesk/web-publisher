<?php

namespace SWP\Bundle\ContentBundle\Factory\ORM;

use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\ArticleInterface;
use SWP\Bundle\ContentBundle\Factory\AbstractArticleFactory;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

class ArticleFactory extends AbstractArticleFactory
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
     * ArticleFactory constructor.
     *
     * @param FactoryInterface       $baseFactory
     * @param RouteProviderInterface $routeProvider
     */
    public function __construct(
        FactoryInterface $baseFactory,
        RouteProviderInterface $routeProvider
    ) {
        parent::__construct($routeProvider);

        $this->baseFactory = $baseFactory;
        $this->routeProvider = $routeProvider;
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
        /** @var ArticleInterface $article */
        $article = $this->hydrateArticle($package);

        return $article;
    }
}
