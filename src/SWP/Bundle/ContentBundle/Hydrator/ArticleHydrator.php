<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Hydrator;

use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Component\Bridge\Model\ItemInterface;
use SWP\Component\Bridge\Model\PackageInterface;

final class ArticleHydrator implements ArticleHydratorInterface
{
    /**
     * @var RouteProviderInterface
     */
    private $routeProvider;

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
     * ArticleHydrator constructor.
     *
     * @param RouteProviderInterface $routeProvider
     */
    public function __construct(RouteProviderInterface $routeProvider)
    {
        $this->routeProvider = $routeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(ArticleInterface $article, PackageInterface $package): ArticleInterface
    {
        $article->setBody($this->populateBody($package));
        $article->setTitle($package->getHeadline());
        if (null !== $package->getSlugline()) {
            $article->setSlug($package->getSlugline());
        }

        $article->setLocale($package->getLanguage());
        $article->setLead($this->populateLead($package));
        $article->setMetadata($package->getMetadata());
        // assign default route
        $article->setRoute($this->routeProvider->getRouteForArticle($article));

        return $article;
    }

    /**
     * @param PackageInterface $package
     *
     * @return string
     */
    protected function populateLead(PackageInterface $package)
    {
        if (null === $package->getDescription() || '' === $package->getDescription()) {
            return trim($package->getDescription().implode('', array_map(function (ItemInterface $item) {
                $this->ensureTypeIsAllowed($item->getType());

                return ' '.$item->getDescription();
            }, $package->getItems()->toArray())));
        }

        return $package->getDescription();
    }

    /**
     * @param PackageInterface $package
     *
     * @return string
     */
    protected function populateBody(PackageInterface $package)
    {
        return $package->getBody().' '.implode('', array_map(function (ItemInterface $item) {
            $this->ensureTypeIsAllowed($item->getType());

            return $item->getBody();
        }, $package->getItems()->toArray()));
    }

    /**
     * @param string $type
     *
     * @throws \InvalidArgumentException
     */
    protected function ensureTypeIsAllowed(string $type)
    {
        if (!in_array($type, $this->allowedTypes)) {
            throw new \InvalidArgumentException(sprintf(
                'Item type "%s" is not supported. Supported types are: %s',
                $type,
                implode(', ', $this->allowedTypes)
            ));
        }
    }
}
