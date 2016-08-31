<?php

/**
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */
namespace SWP\Bundle\ContentBundle\Factory;

use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\ArticleInterface;
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
     * @var array
     */
    private $allowedTypes = [
        ItemInterface::TYPE_PICTURE,
        ItemInterface::TYPE_FILE,
        ItemInterface::TYPE_TEXT,
        ItemInterface::TYPE_COMPOSITE,
    ];

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
        /** @var ArticleInterface $article */
        $article = $this->create();


        $article->setBody($this->populateBody($package));
        $article->setParentDocument($this->articleProvider->getParent($this->contentRelativePath));
        $article->setTitle($package->getHeadline());
        $article->setLocale($package->getLanguage());
        $article->setRoute($this->routeProvider->getRouteForArticle($article));
        $article->setMetadata($package->getMetadata());

        return $article;
    }

    private function populateBody(PackageInterface $package)
    {
        return $package->getBody().' '.implode('', array_map(function (ItemInterface $item) {
            $this->ensureTypeIsAllowed($item->getType());

            return $item->getBody();
        }, $package->getItems()->toArray()));
    }

    private function ensureTypeIsAllowed($type)
    {
        if (!in_array($type, $this->allowedTypes)) {
            throw new \InvalidArgumentException(sprintf(
                'Item type "%s" is not supported. Supported types are: %s',
                $type,
                implode(', ', $this->allowedTypes))
            );
        }
    }
}
